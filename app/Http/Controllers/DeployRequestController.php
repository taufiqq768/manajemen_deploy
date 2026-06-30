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

        $user = auth()->user();
        if ($user->isProgrammer()) {
            $applications = Application::whereHas('pics', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })->orderBy('name')->get();
        } else {
            $applications = Application::orderBy('name')->get();
        }

        return view('deploy-requests.create', compact('applications'));
    }

    /** Simpan request baru */
    public function store(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'application_id' => [
                'required',
                'exists:applications,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->isProgrammer()) {
                        $isPic = \DB::table('application_user')
                            ->where('application_id', $value)
                            ->where('user_id', $user->id)
                            ->exists();
                        if (!$isPic) {
                            $fail('Anda bukan PIC untuk aplikasi ini.');
                        }
                    }
                }
            ],
            'jenis' => 'required|array|min:1',
            'jenis.*' => 'in:perubahan_besar,perubahan_kecil,bug_fixing',
            'version' => 'required|string|max:50',
            'release_notes' => 'required|array',
            'release_notes.perubahan_besar' => 'required_if:jenis.*,perubahan_besar|nullable|string',
            'release_notes.perubahan_kecil' => 'required_if:jenis.*,perubahan_kecil|nullable|string',
            'release_notes.bug_fixing' => 'required_if:jenis.*,bug_fixing|nullable|string',
            'release_impact' => 'nullable|string',
            'document_support' => 'nullable|file|mimes:pdf,doc,docx,jpg,txt,png|max:2048', // max 2MB
            'scheduled_at' => 'nullable|date',
        ]);

        $docPath = null;
        if ($request->hasFile('document_support')) {
            $docPath = $request->file('document_support')->store('documents', 'public');
        }

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
                // fallback if ticket_number format is broken, just count
                $urut = DeployRequest::whereYear('created_at', $year)->whereMonth('created_at', $month)->count() + 1;
            }
        }
        
        $nomorUrut = str_pad($urut, 4, '0', STR_PAD_LEFT);
        $ticketNumber = "DM/{$year}/{$romanMonth}/{$nomorUrut}";

        $deploy = DeployRequest::create([
            ...$validated,
            'ticket_number' => $ticketNumber,
            'document_support' => $docPath,
            'requester_id' => auth()->id(),
            'status' => 'pending',
            'environment' => 'production',
        ]);

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

    /** Form edit (hanya jika pending & milik sendiri) */
    public function edit(DeployRequest $deployRequest)
    {
        $this->authorize('update', $deployRequest);

        $user = auth()->user();
        if ($user->isProgrammer()) {
            $applications = Application::whereHas('pics', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })->orderBy('name')->get();
        } else {
            $applications = Application::orderBy('name')->get();
        }

        return view('deploy-requests.edit', compact('deployRequest', 'applications'));
    }

    /** Simpan perubahan (revisi) */
    public function update(Request $request, DeployRequest $deployRequest)
    {
        $this->authorize('update', $deployRequest);

        $user = auth()->user();
        $validated = $request->validate([
            'application_id' => [
                'required',
                'exists:applications,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->isProgrammer()) {
                        $isPic = \DB::table('application_user')
                            ->where('application_id', $value)
                            ->where('user_id', $user->id)
                            ->exists();
                        if (!$isPic) {
                            $fail('Anda bukan PIC untuk aplikasi ini.');
                        }
                    }
                }
            ],
            'jenis' => 'required|array|min:1',
            'jenis.*' => 'in:perubahan_besar,perubahan_kecil,bug_fixing',
            'version' => 'required|string|max:50',
            'release_notes' => 'required|array',
            'release_notes.perubahan_besar' => 'required_if:jenis.*,perubahan_besar|nullable|string',
            'release_notes.perubahan_kecil' => 'required_if:jenis.*,perubahan_kecil|nullable|string',
            'release_notes.bug_fixing' => 'required_if:jenis.*,bug_fixing|nullable|string',
            'release_impact' => 'nullable|string',
            'document_support' => 'nullable|file|mimes:pdf,doc,docx,jpg,txt,png|max:2048',
            'scheduled_at' => 'nullable|date',
        ]);

        $data = $validated;
        if ($request->hasFile('document_support')) {
            if ($deployRequest->document_support) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($deployRequest->document_support);
            }
            $data['document_support'] = $request->file('document_support')->store('documents', 'public');
        } else {
            // Biarkan dokumen lama jika tidak ada file baru di-upload
            unset($data['document_support']);
        }

        $deployRequest->update([
            ...$data,
            'status' => 'pending', // reset ke pending setelah revisi
        ]);

        return redirect()->route('deploy-requests.show', $deployRequest)
            ->with('success', 'Request deploy berhasil diperbarui.');
    }

    /** Approve (Project Manager) */
    public function approve(DeployRequest $deployRequest)
    {
        $this->authorize('decide', $deployRequest);

        $deployRequest->update([
            'status' => 'approved',
            'approver_id' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Update versi aplikasi di database internal
        $application = $deployRequest->application;
        $application->update([
            'version' => $deployRequest->version,
            'synced_at' => now(),
        ]);

        // Panggil API Write jika dikonfigurasi
        $apiError = null;
        if ($application->version_api_write) {
            try {
                $writeKey = $application->version_api_write_key ?: 'version';
                $notesKey = $application->version_api_write_notes_key ?: 'release_notes';
                
                $releaseNotesRaw = $deployRequest->release_notes;
                $releaseNotesText = '';
                if (is_array($releaseNotesRaw)) {
                    $lines = [];
                    if (!empty($releaseNotesRaw['perubahan_besar'])) {
                        $lines[] = "• Perubahan Besar:\n" . $releaseNotesRaw['perubahan_besar'];
                    }
                    if (!empty($releaseNotesRaw['perubahan_kecil'])) {
                        $lines[] = "• Perubahan Kecil:\n" . $releaseNotesRaw['perubahan_kecil'];
                    }
                    if (!empty($releaseNotesRaw['bug_fixing'])) {
                        $lines[] = "• Bug Fixing:\n" . $releaseNotesRaw['bug_fixing'];
                    }
                    $releaseNotesText = implode("\n\n", $lines);
                } else {
                    $releaseNotesText = (string) $releaseNotesRaw;
                }
                
                $payload = [
                    $writeKey => $deployRequest->version,
                    $notesKey => $releaseNotesText,
                ];
                
                $response = \Illuminate\Support\Facades\Http::timeout(5)
                    ->post($application->version_api_write, $payload);
                    
                if (!$response->successful()) {
                    $apiError = "Respon HTTP " . $response->status();
                    \Illuminate\Support\Facades\Log::warning("Gagal push versi ke API Write {$application->name}: " . $apiError);
                }
            } catch (\Throwable $e) {
                $apiError = "Koneksi gagal";
                \Illuminate\Support\Facades\Log::warning("Gagal push versi ke API Write {$application->name}: " . $e->getMessage());
            }
        }

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

        $successMsg = 'Request deploy telah disetujui.';
        if ($apiError) {
            $successMsg .= " (Peringatan: Gagal memperbarui versi via API Write: {$apiError})";
        }

        return back()->with('success', $successMsg);
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
