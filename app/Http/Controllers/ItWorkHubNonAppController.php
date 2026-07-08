<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItWhNonappProject;
use App\Models\ItWhNonappActivity;
use App\Models\ItWhNonappDocument;
use App\Models\User;

class ItWorkHubNonAppController extends Controller
{
    public function longlist(Request $request)
    {
        $query = ItWhNonappProject::with('squads');

        // Search logic
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $projects = $query->orderBy('sort_order', 'asc')->paginate(10);

        // Stats
        $stats = [
            'total' => ItWhNonappProject::count(),
            'not_started' => ItWhNonappProject::where('status', 'Not Started')->count(),
            'live' => ItWhNonappProject::where('status', 'Live')->count(),
            'live_cr' => ItWhNonappProject::where('status', 'Live w/ CR')->count(),
            'live_bug' => ItWhNonappProject::where('status', 'Live w/ Bug')->count(),
            'hold' => ItWhNonappProject::whereIn('status', ['Hold', 'Retired'])->count(),
        ];

        return view('it-work-hub.non-app.longlist', compact('projects', 'stats'));
    }

    public function create()
    {
        // Ambil data user selain admin dan project_manager untuk Squad
        $users = User::whereNotIn('role', ['admin', 'project_manager'])->get();

        return view('it-work-hub.non-app.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:High,Medium,Low',
            'status' => 'required|string',
            'squads' => 'required|array',
            'squads.*' => 'exists:users,id',
            'bpo' => 'nullable|string|max:255',
            'progress' => 'required|integer|min:0|max:100',
            'pain_point_uraian' => 'nullable|string',
            'pain_point_impact' => 'nullable|string',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'adjustment_date' => 'nullable|date',
        ]);

        $project = ItWhNonappProject::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'bpo' => $validated['bpo'],
            'progress' => $validated['progress'],
            'pain_point_uraian' => $validated['pain_point_uraian'],
            'pain_point_impact' => $validated['pain_point_impact'],
            'start_date' => $validated['start_date'],
            'deadline' => $validated['deadline'],
            'adjustment_date' => $validated['adjustment_date'],
            'sort_order' => ItWhNonappProject::max('sort_order') + 1,
        ]);

        $project->squads()->attach($validated['squads']);

        return redirect()->route('it-work-hub.non-app.longlist')->with('success', 'Project Non App baru berhasil ditambahkan.');
    }

    public function show($id)
    {
        $project = ItWhNonappProject::with(['squads', 'documents'])->findOrFail($id);
        return view('it-work-hub.non-app.show', compact('project'));
    }

    public function activities($id)
    {
        $project = ItWhNonappProject::with(['squads', 'activities.pics'])->findOrFail($id);
        $users = User::whereNotIn('role', ['admin', 'project_manager'])->get();
        return view('it-work-hub.non-app.activities', compact('project', 'users'));
    }

    public function updateActivities(Request $request, $id)
    {
        $project = ItWhNonappProject::findOrFail($id);
        
        \Illuminate\Support\Facades\Log::info('Incoming activities payload', $request->all());

        try {
            $request->validate([
                'activities' => 'present|array',
            'activities.*.id' => 'nullable',
            'activities.*.name' => 'required|string',
            'activities.*.start_date' => 'nullable|date',
            'activities.*.deadline' => 'nullable|date',
            'activities.*.adjustment_date' => 'nullable|date',
            'activities.*.notes' => 'nullable|string',
            'activities.*.status' => 'required|string',
            'activities.*.sort_order' => 'required|integer',
            'activities.*.pics' => 'nullable|array',
            'activities.*.pics.*' => 'exists:users,id',
        ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Validation failed', $e->errors());
            throw $e;
        }

        try {
            $incomingIds = [];
        $activitiesData = $request->input('activities', []);

        foreach ($activitiesData as $actData) {
            $activity = ItWhNonappActivity::updateOrCreate(
                ['id' => (isset($actData['id']) && is_numeric($actData['id'])) ? $actData['id'] : null, 'it_wh_nonapp_project_id' => $project->id],
                [
                    'name' => $actData['name'],
                    'start_date' => $actData['start_date'] ?: null,
                    'deadline' => $actData['deadline'] ?: null,
                    'adjustment_date' => $actData['adjustment_date'] ?: null,
                    'notes' => $actData['notes'] ?? null,
                    'status' => $actData['status'],
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
        $project->activities()->whereNotIn('id', $incomingIds)->delete();

        // Project progress is automatically recalculated via Model Events (booted in ItWhNonappActivity)

        // Refetch project to get the newly calculated progress
        $project->refresh();

        return response()->json(['success' => true, 'message' => 'Aktivitas berhasil disimpan.', 'progress' => $project->progress]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Save activities failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function storeDocument(Request $request, $id)
    {
        $project = ItWhNonappProject::findOrFail($id);

        $request->validate([
            'document_id' => 'nullable|integer',
            'description' => 'required|string',
            'document_date' => 'nullable|date',
            'link' => 'nullable|url',
            'file' => 'nullable|file|max:10240', // max 10MB
        ]);

        $document = null;
        if ($request->document_id) {
            $document = ItWhNonappDocument::where('it_wh_nonapp_project_id', $project->id)->find($request->document_id);
        }

        if (!$document) {
            $document = new ItWhNonappDocument();
            $document->it_wh_nonapp_project_id = $project->id;
        }

        $document->description = $request->description;
        $document->document_date = $request->document_date ?: null;
        $document->link = $request->link ?: null;

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($document->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($document->file_path);
            }

            $path = $request->file('file')->store('it-work-hub/non-app/documents', 'public');
            $document->file_path = $path;
        }

        $document->save();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil disimpan.',
            'document' => [
                'id' => $document->id,
                'description' => $document->description,
                'document_date' => $document->document_date ? \Carbon\Carbon::parse($document->document_date)->format('d M Y') : '-',
                'file_path' => $document->file_path,
                'file_name' => $document->file_path ? basename($document->file_path) : null,
                'file_url' => $document->file_path ? asset('storage/' . $document->file_path) : null,
                'link' => $document->link,
            ]
        ]);
    }

    public function destroyDocument($id)
    {
        $document = ItWhNonappDocument::findOrFail($id);
        
        if ($document->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();

        return response()->json(['success' => true, 'message' => 'Dokumen berhasil dihapus.']);
    }
}
