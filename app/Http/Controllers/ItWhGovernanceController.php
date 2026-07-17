<?php

namespace App\Http\Controllers;

use App\Models\ItWhGovernance;
use App\Models\ItWhGovernanceActivity;
use App\Models\ItWhGovernanceDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ItWhGovernanceController extends Controller
{
    public function longlist()
    {
        $governances = ItWhGovernance::with('pics')->orderBy('sort_order', 'asc')->get();
        $users = User::whereNotIn('role', ['admin', 'project_manager'])->get();
        return view('it-work-hub.governance.longlist', compact('governances', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:High,Medium,Low',
            'pics' => 'nullable|array',
            'pics.*' => 'exists:users,id',
            'progress_notes' => 'nullable|string',
            'progress_date' => 'nullable|date',
        ]);

        $maxOrder = ItWhGovernance::max('sort_order') ?? 0;

        $gov = ItWhGovernance::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'progress' => 0,
            'progress_notes' => $validated['progress_notes'] ?? null,
            'progress_date' => $validated['progress_date'] ?? null,
            'sort_order' => $maxOrder + 1,
        ]);

        if (isset($validated['pics'])) {
            $gov->pics()->sync($validated['pics']);
        }

        return redirect()->route('it-work-hub.governance.longlist')->with('success', 'Task Governance berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:High,Medium,Low',
            'pics' => 'nullable|array',
            'pics.*' => 'exists:users,id',
            'progress_notes' => 'nullable|string',
            'progress_date' => 'nullable|date',
        ]);

        $gov = ItWhGovernance::findOrFail($id);
        $gov->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'progress_notes' => $validated['progress_notes'] ?? null,
            'progress_date' => $validated['progress_date'] ?? null,
        ]);

        if (isset($validated['pics'])) {
            $gov->pics()->sync($validated['pics']);
        } else {
            $gov->pics()->detach();
        }

        return redirect()->back()->with('success', 'Informasi Governance berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $gov = ItWhGovernance::findOrFail($id);
        
        $gov->pics()->detach();
        $gov->activities()->delete();
        $gov->documents()->delete();

        $gov->delete();

        return redirect()->route('it-work-hub.governance.longlist')->with('success', 'Task Governance berhasil dihapus.');
    }

    public function updateSortOrder(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $index => $id) {
            ItWhGovernance::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $gov = ItWhGovernance::with(['pics', 'activities', 'documents'])->findOrFail($id);
        $users = User::whereNotIn('role', ['admin', 'project_manager'])->get();
        return view('it-work-hub.governance.show', compact('gov', 'users'));
    }

    public function activities($id)
    {
        $gov = ItWhGovernance::with(['pics', 'activities.pics', 'activities.status'])->findOrFail($id);
        $users = User::whereNotIn('role', ['admin', 'project_manager'])->get();
        $statuses = \App\Models\ItWhMasterStatus::where('category', 'Governance')->where('is_active', true)->orderBy('sort_order')->get();
        return view('it-work-hub.governance.activities', compact('gov', 'users', 'statuses'));
    }

    public function updateActivities(Request $request, $id)
    {
        $gov = ItWhGovernance::findOrFail($id);
        
        try {
            $request->validate([
                'activities' => 'present|array',
                'activities.*.id' => 'nullable',
                'activities.*.name' => 'required|string',
                'activities.*.start_date' => 'nullable|date',
                'activities.*.deadline' => 'nullable|date',
                'activities.*.adjustment_date' => 'nullable|date',
                'activities.*.notes' => 'nullable|string',
                'activities.*.status_id' => 'required|exists:it_wh_master_statuses,id',
                'activities.*.progress' => 'required|integer|min:0|max:100',
                'activities.*.sort_order' => 'required|integer',
                'activities.*.pics' => 'nullable|array',
                'activities.*.pics.*' => 'exists:users,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', $e->errors());
            throw $e;
        }

        try {
            $incomingIds = [];
            $activitiesData = $request->input('activities', []);

            foreach ($activitiesData as $actData) {
                $activity = ItWhGovernanceActivity::updateOrCreate(
                    ['id' => (isset($actData['id']) && is_numeric($actData['id'])) ? $actData['id'] : null, 'it_wh_governance_id' => $gov->id],
                    [
                        'name' => $actData['name'],
                        'start_date' => $actData['start_date'] ?: null,
                        'deadline' => $actData['deadline'] ?: null,
                        'adjustment_date' => $actData['adjustment_date'] ?: null,
                        'notes' => $actData['notes'] ?? null,
                        'status_id' => $actData['status_id'],
                        'progress' => $actData['progress'],
                        'sort_order' => $actData['sort_order'],
                    ]
                );

                if (isset($actData['pics'])) {
                    $activity->pics()->sync($actData['pics']);
                } else {
                    $activity->pics()->detach();
                }

                $incomingIds[] = $activity->id;
            }

            // Delete activities that are no longer in the payload
            $gov->activities()->whereNotIn('id', $incomingIds)->delete();

            return response()->json(['success' => true, 'message' => 'Detail Aktivitas berhasil disimpan.']);
        } catch (\Throwable $e) {
            Log::error('Save activities failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function storeDocument(Request $request, $id)
    {
        $gov = ItWhGovernance::findOrFail($id);

        $request->validate([
            'document_id' => 'nullable|integer',
            'description' => 'required|string',
            'document_date' => 'nullable|date',
            'link' => 'nullable|url',
            'file' => 'nullable|file|max:10240', // max 10MB
        ]);

        $document = null;
        if ($request->document_id) {
            $document = ItWhGovernanceDocument::where('it_wh_governance_id', $gov->id)->find($request->document_id);
        }

        if (!$document) {
            $document = new ItWhGovernanceDocument();
            $document->it_wh_governance_id = $gov->id;
        }

        $document->description = $request->description;
        $document->document_date = $request->document_date ?: null;
        $document->link = $request->link ?: null;

        if ($request->hasFile('file')) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $path = $request->file('file')->store('it-work-hub/governance/documents', 'public');
            $document->file_path = $path;
        }

        $document->save();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil disimpan.',
            'document' => [
                'id' => $document->id,
                'description' => $document->description,
                'document_date' => $document->document_date ? Carbon::parse($document->document_date)->format('d M Y') : '-',
                'file_path' => $document->file_path,
                'file_name' => $document->file_path ? basename($document->file_path) : null,
                'file_url' => $document->file_path ? asset('storage/' . $document->file_path) : null,
                'link' => $document->link,
            ]
        ]);
    }

    public function destroyDocument($id)
    {
        $document = ItWhGovernanceDocument::findOrFail($id);
        
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();

        return response()->json(['success' => true, 'message' => 'Dokumen berhasil dihapus.']);
    }
}
