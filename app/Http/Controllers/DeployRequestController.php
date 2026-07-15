<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\DeployRequest;
use App\Models\User;
use App\Services\AppSyncService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class DeployRequestController extends Controller
{
    public function __construct(
        protected NotificationService $notif,
        protected AppSyncService $appSync,
    ) {
    }

    /** List semua request (filter by role & status) */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = DeployRequest::with(['application', 'requester', 'approver']);

        if ($user->isProgrammer()) {
            $query->where('requester_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('application_id')) {
            $query->where('application_id', $request->application_id);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('jenis')) {
            $query->whereJsonContains('jenis', $request->jenis);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $deployRequests = $query->latest()->paginate(10)->withQueryString();
        $applications = Application::orderBy('name')->get();

        return view('deploy-requests.index', compact('deployRequests', 'applications'));
    }

    /** Form buat request baru — sync daftar aplikasi dari API terlebih dahulu */
    public function create()
    {
        // Sync dari API; jika gagal, form tetap tampil dengan data DB yang ada
        $syncResult = $this->appSync->sync();
        if (!$syncResult->isOk()) {
            session()->flash('warning', 'Sync daftar aplikasi dari API gagal: ' . $syncResult->summary());
        }

        $applications = Application::orderBy('name')->get();

        return view('deploy-requests.create', compact('applications'));
    }

    /** Simpan request baru */
    public function store(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'kategori' => 'required|in:cr,enhancement,bug_fixing',
            'jenis' => 'required|array|min:1',
            'jenis.*' => 'in:perubahan_besar,perubahan_kecil,bug_fixing',
            'version' => 'required|string|max:50',
            'release_notes' => 'required|array',
            'release_notes.perubahan_besar' => 'required_if:jenis.*,perubahan_besar|nullable|string',
            'release_notes.perubahan_kecil' => 'required_if:jenis.*,perubahan_kecil|nullable|string',
            'release_notes.bug_fixing' => 'required_if:jenis.*,bug_fixing|nullable|string',
            'release_impact' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'documents' => 'nullable|array',
            'documents.*.number' => 'nullable|string|max:150',
            'documents.*.file' => 'nullable|file|mimes:pdf,doc,docx,jpg,txt,png|max:2048',
        ]);

        $now = now();
        $year = $now->format('Y');
        $month = $now->format('n');
        
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $romanMonth = $romanMonths[$month];

        // Get last request of the current month
        $lastRequest = DeployRequest::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $urut = 1;
        if ($lastRequest && $lastRequest->ticket_number) {
            $parts = explode('/', $lastRequest->ticket_number);
            if (count($parts) == 4) {
                $urut = (int) $parts[3] + 1;
            } else {
                $urut = DeployRequest::whereYear('created_at', $year)->whereMonth('created_at', $month)->count() + 1;
            }
        }
        
        $nomorUrut = str_pad($urut, 4, '0', STR_PAD_LEFT);
        $ticketNumber = "DM/{$year}/{$romanMonth}/{$nomorUrut}";

        // Filter out documents before save to avoid mass assignment issues on DeployRequest
        $createData = collect($validated)->except(['documents'])->toArray();

        $deploy = DeployRequest::create([
            ...$createData,
            'ticket_number' => $ticketNumber,
            'requester_id' => auth()->id(),
            'status' => 'pending',
            'environment' => 'production',
        ]);

        // Simpan dokumen-dokumen terkait
        if ($request->has('documents')) {
            foreach ($request->input('documents') as $key => $docData) {
                $docNumber = $docData['number'] ?? null;
                $docPath = null;
                
                if ($request->hasFile("documents.{$key}.file")) {
                    $docPath = $request->file("documents.{$key}.file")->store('documents', 'public');
                }
                
                if ($docNumber || $docPath) {
                    $deploy->documents()->create([
                        'document_number' => $docNumber,
                        'file_path' => $docPath,
                    ]);
                }
            }
        }

        // Kirim notifikasi ke semua Project Manager (in-app + WA + email)
        $appName = $deploy->application->name;
        $pms = User::where('role', 'project_manager')->get();

        foreach ($pms as $pm) {
            $this->notif->send(
                user: $pm,
                deployRequestId: $deploy->id,
                title: "[{$deploy->ticket_number}] Request Deploy Baru 🚀",
                message: "Request deploy *{$appName}* {$deploy->version} menunggu persetujuan Anda.",
                detail: "Diajukan oleh: " . auth()->user()->name,
                type: 'new',
            );
        }

        return redirect()->route('deploy-requests.index')
            ->with('success', 'Request deploy berhasil diajukan.');
    }

    /** Detail request */
    public function show(DeployRequest $deployRequest)
    {
        $deployRequest->load(['application', 'requester', 'approver']);

        return view('deploy-requests.show', compact('deployRequest'));
    }

    public function edit(DeployRequest $deployRequest)
    {
        $this->authorize('update', $deployRequest);

        $applications = Application::orderBy('name')->get();

        return view('deploy-requests.edit', compact('deployRequest', 'applications'));
    }

    /** Simpan perubahan (revisi) */
    public function update(Request $request, DeployRequest $deployRequest)
    {
        $this->authorize('update', $deployRequest);

        $user = auth()->user();
        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'kategori' => 'required|in:cr,enhancement,bug_fixing',
            'jenis' => 'required|array|min:1',
            'jenis.*' => 'in:perubahan_besar,perubahan_kecil,bug_fixing',
            'version' => 'required|string|max:50',
            'release_notes' => 'required|array',
            'release_notes.perubahan_besar' => 'required_if:jenis.*,perubahan_besar|nullable|string',
            'release_notes.perubahan_kecil' => 'required_if:jenis.*,perubahan_kecil|nullable|string',
            'release_notes.bug_fixing' => 'required_if:jenis.*,bug_fixing|nullable|string',
            'release_impact' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'documents' => 'nullable|array',
            'documents.*.id' => 'nullable|integer|exists:deploy_request_documents,id',
            'documents.*.number' => 'nullable|string|max:150',
            'documents.*.file' => 'nullable|file|mimes:pdf,doc,docx,jpg,txt,png|max:2048',
        ]);

        $updateData = collect($validated)->except(['documents'])->toArray();

        $deployRequest->update([
            ...$updateData,
            'status' => 'pending', // reset ke pending setelah revisi
        ]);

        // Simpan / update dokumen-dokumen terkait
        $submittedIds = [];
        if ($request->has('documents')) {
            foreach ($request->input('documents') as $key => $docData) {
                $docId = $docData['id'] ?? null;
                $docNumber = $docData['number'] ?? null;
                
                $existingDoc = $docId ? $deployRequest->documents()->find($docId) : null;
                $docPath = $existingDoc ? $existingDoc->file_path : null;
                
                if ($request->hasFile("documents.{$key}.file")) {
                    if ($existingDoc && $existingDoc->file_path) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($existingDoc->file_path);
                    }
                    $docPath = $request->file("documents.{$key}.file")->store('documents', 'public');
                }
                
                if ($docNumber || $docPath) {
                    if ($existingDoc) {
                        $existingDoc->update([
                            'document_number' => $docNumber,
                            'file_path' => $docPath,
                        ]);
                        $submittedIds[] = $existingDoc->id;
                    } else {
                        $newDoc = $deployRequest->documents()->create([
                            'document_number' => $docNumber,
                            'file_path' => $docPath,
                        ]);
                        $submittedIds[] = $newDoc->id;
                    }
                }
            }
        }

        // Hapus dokumen yang dibuang oleh user di UI
        $docsToDelete = $deployRequest->documents()->whereNotIn('id', $submittedIds)->get();
        foreach ($docsToDelete as $delDoc) {
            if ($delDoc->file_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($delDoc->file_path);
            }
            $delDoc->delete();
        }

        return redirect()->route('deploy-requests.show', $deployRequest)
            ->with('success', 'Request deploy berhasil diperbarui.');
    }

    /** Approve (Project Manager) */
    public function approve(DeployRequest $deployRequest)
    {
        $this->authorize('decide', $deployRequest);

        $application = $deployRequest->application;
        
        $oldVersion = $application->version;

        // Hitung kenaikan versi semantic pada saat disetujui (PM approval)
        $newVersion = DeployRequest::calculateBumpedVersion($oldVersion ?? '0.0.0', $deployRequest->jenis);

        $deployRequest->update([
            'status' => 'approved',
            'approver_id' => auth()->id(),
            'approved_at' => now(),
            'version' => $newVersion, // simpan versi baru hasil bumping ke record request
        ]);

        // Update versi aplikasi di database internal ke versi baru
        $application->update([
            'version' => $newVersion,
            'synced_at' => now(),
        ]);

        // Beritahu requester (programmer) via in-app + WA + email
        $requester = $deployRequest->requester;
        $appName = $application->name;

        $this->notif->send(
            user: $requester,
            deployRequestId: $deployRequest->id,
            title: "[{$deployRequest->ticket_number}] Deploy Disetujui ✅",
            message: "Request deploy *{$appName}* {$deployRequest->version} telah *disetujui*.",
            detail: "Disetujui oleh: " . auth()->user()->name,
            type: 'approved',
        );

        return back()->with('success', 'Request deploy telah disetujui.');
    }

    /** Reject (Project Manager) */
    public function reject(Request $request, DeployRequest $deployRequest)
    {
        $this->authorize('decide', $deployRequest);

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $deployRequest->update([
            'status' => 'rejected',
            'approver_id' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Beritahu requester (programmer) via in-app + WA + email
        $requester = $deployRequest->requester;
        $appName = $deployRequest->application->name;

        $this->notif->send(
            user: $requester,
            deployRequestId: $deployRequest->id,
            title: "[{$deployRequest->ticket_number}] Deploy Ditolak ❌",
            message: "Request deploy *{$appName}* {$deployRequest->version} telah *ditolak*.",
            detail: "Alasan: {$request->rejection_reason}",
            type: 'rejected',
        );

        return back()->with('error', 'Request deploy telah ditolak.');
    }
}
