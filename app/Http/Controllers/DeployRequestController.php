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
        protected AppSyncService      $appSync,
    ) {}

    /** List semua request (filter by role & status) */
    public function index(Request $request)
    {
        $user  = auth()->user();
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

        $deployRequests = $query->latest()->paginate(10)->withQueryString();
        $applications   = Application::orderBy('name')->get();

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

        $user         = auth()->user();
        $applications = Application::orderBy('name')->get(); // semua programmer akses semua app

        return view('deploy-requests.create', compact('applications'));
    }

    /** Simpan request baru */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'application_id'   => 'required|exists:applications,id',
            'version'          => 'required|string|max:50',
            'release_notes'    => 'required|string',
            'release_impact'   => 'nullable|string',
            'document_support' => 'nullable|file|mimes:pdf,doc,docx,jpg,txt,png|max:2048', // max 2MB
            'scheduled_at'     => 'nullable|date',
        ]);

        $docPath = null;
        if ($request->hasFile('document_support')) {
            $docPath = $request->file('document_support')->store('documents', 'public');
        }

        $deploy = DeployRequest::create([
            ...$validated,
            'document_support' => $docPath,
            'requester_id' => auth()->id(),
            'status'       => 'pending',
            'environment'  => 'production',
        ]);

        // Kirim notifikasi ke semua Project Manager (in-app + WA + email)
        $appName = $deploy->application->name;
        $pms     = User::where('role', 'project_manager')->get();

        foreach ($pms as $pm) {
            $this->notif->send(
                user:            $pm,
                deployRequestId: $deploy->id,
                title:           'Request Deploy Baru 🚀',
                message:         "Request deploy *{$appName}* v{$deploy->version} menunggu persetujuan Anda.",
                detail:          "Diajukan oleh: " . auth()->user()->name,
                type:            'new',
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

        $applications = Application::orderBy('name')->get(); // semua app tersedia

        return view('deploy-requests.edit', compact('deployRequest', 'applications'));
    }

    /** Simpan perubahan (revisi) */
    public function update(Request $request, DeployRequest $deployRequest)
    {
        $this->authorize('update', $deployRequest);

        $validated = $request->validate([
            'application_id'   => 'required|exists:applications,id',
            'version'          => 'required|string|max:50',
            'release_notes'    => 'required|string',
            'release_impact'   => 'nullable|string',
            'document_support' => 'nullable|file|mimes:pdf,doc,docx,jpg,txt,png|max:2048',
            'scheduled_at'     => 'nullable|date',
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
            'status'      => 'approved',
            'approver_id' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Beritahu requester (programmer) via in-app + WA + email
        $requester = $deployRequest->requester;
        $appName   = $deployRequest->application->name;

        $this->notif->send(
            user:            $requester,
            deployRequestId: $deployRequest->id,
            title:           'Deploy Disetujui ✅',
            message:         "Request deploy *{$appName}* v{$deployRequest->version} telah *disetujui*.",
            detail:          "Disetujui oleh: " . auth()->user()->name,
            type:            'approved',
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
            'status'           => 'rejected',
            'approver_id'      => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Beritahu requester (programmer) via in-app + WA + email
        $requester = $deployRequest->requester;
        $appName   = $deployRequest->application->name;

        $this->notif->send(
            user:            $requester,
            deployRequestId: $deployRequest->id,
            title:           'Deploy Ditolak ❌',
            message:         "Request deploy *{$appName}* v{$deployRequest->version} telah *ditolak*.",
            detail:          "Alasan: {$request->rejection_reason}",
            type:            'rejected',
        );

        return back()->with('error', 'Request deploy telah ditolak.');
    }
}
