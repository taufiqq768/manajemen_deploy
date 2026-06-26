<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItWorkHubController extends Controller
{
    public function dashboard()
    {
        return view('it-work-hub.dashboard');
    }

    public function longlist(Request $request)
    {
        $query = \App\Models\ItWhProject::with('squads');

        // Search logic (optional if added later)
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $projects = $query->orderBy('sort_order', 'asc')->paginate(10);

        // Stats
        $stats = [
            'total' => \App\Models\ItWhProject::count(),
            'not_started' => \App\Models\ItWhProject::where('status', 'Not Started')->count(),
            'live' => \App\Models\ItWhProject::where('status', 'Live')->count(),
            'live_cr' => \App\Models\ItWhProject::where('status', 'Live w/ CR')->count(),
            'live_bug' => \App\Models\ItWhProject::where('status', 'Live w/ Bug')->count(),
            'hold' => \App\Models\ItWhProject::whereIn('status', ['Hold', 'Retired'])->count(),
        ];

        return view('it-work-hub.longlist', compact('projects', 'stats'));
    }

    public function create()
    {
        // Ambil data user selain admin dan project_manager untuk Squad
        $users = \App\Models\User::whereNotIn('role', ['admin', 'project_manager'])->get();

        return view('it-work-hub.create', compact('users'));
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
        ]);

        $project = \App\Models\ItWhProject::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'bpo' => $validated['bpo'],
            'progress' => $validated['progress'],
            'pain_point_uraian' => $validated['pain_point_uraian'],
            'pain_point_impact' => $validated['pain_point_impact'],
            'sort_order' => \App\Models\ItWhProject::max('sort_order') + 1,
        ]);

        $project->squads()->attach($validated['squads']);

        return redirect()->route('it-work-hub.longlist')->with('success', 'Project baru berhasil ditambahkan.');
    }

    public function show($id)
    {
        $project = \App\Models\ItWhProject::with(['squads', 'documents'])->findOrFail($id);
        return view('it-work-hub.show', compact('project'));
    }

    public function activities($id)
    {
        $project = \App\Models\ItWhProject::with(['squads', 'activities.pics'])->findOrFail($id);
        $users = \App\Models\User::whereNotIn('role', ['admin', 'project_manager'])->get();
        return view('it-work-hub.activities', compact('project', 'users'));
    }

    public function updateActivities(Request $request, $id)
    {
        $project = \App\Models\ItWhProject::findOrFail($id);
        
        $request->validate([
            'activities' => 'present|array',
            'activities.*.id' => 'nullable',
            'activities.*.type' => 'required|in:Fitur,CR,Bug',
            'activities.*.name' => 'required|string',
            'activities.*.start_date' => 'nullable|date',
            'activities.*.end_date' => 'nullable|date',
            'activities.*.adjusted_date' => 'nullable|date',
            'activities.*.description' => 'nullable|string',
            'activities.*.document_link' => 'nullable|url',
            'activities.*.status' => 'required|string',
            'activities.*.sort_order' => 'required|integer',
            'activities.*.pics' => 'nullable|array',
            'activities.*.pics.*' => 'exists:users,id',
        ]);

        $incomingIds = [];
        $activitiesData = $request->input('activities', []);

        foreach ($activitiesData as $actData) {
            $activity = \App\Models\ItWhActivity::updateOrCreate(
                ['id' => (isset($actData['id']) && is_numeric($actData['id'])) ? $actData['id'] : null, 'it_wh_project_id' => $project->id],
                [
                    'type' => $actData['type'],
                    'name' => $actData['name'],
                    'start_date' => $actData['start_date'] ?: null,
                    'end_date' => $actData['end_date'] ?: null,
                    'adjusted_date' => $actData['adjusted_date'] ?: null,
                    'description' => $actData['description'] ?? null,
                    'document_link' => $actData['document_link'] ?? null,
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

        // Recalculate Project Progress
        $allActivities = $project->activities()->get();
        if ($allActivities->count() > 0) {
            $totalWeight = 0;
            foreach ($allActivities as $act) {
                $weight = match($act->status) {
                    'Ureq Analysis' => 15,
                    'Programming' => 50,
                    'Tech Testing' => 70,
                    'SIT' => 85,
                    'UAT' => 95,
                    'Done' => 100,
                    default => 0,
                };
                $totalWeight += $weight;
            }
            $averageProgress = round($totalWeight / $allActivities->count());
            $project->progress = $averageProgress;
        } else {
            $project->progress = 0;
        }
        $project->save();

        return response()->json(['success' => true, 'message' => 'Aktivitas berhasil disimpan.', 'progress' => $project->progress]);
    }

    public function storeDocument(Request $request, $id)
    {
        $project = \App\Models\ItWhProject::findOrFail($id);

        $request->validate([
            'document_id' => 'nullable|integer',
            'type' => 'required|in:PIR,Dokumen',
            'description' => 'required|string',
            'document_date' => 'nullable|date',
            'link' => 'nullable|url',
            'file' => 'nullable|file|max:10240', // max 10MB
        ]);

        $document = null;
        if ($request->document_id) {
            $document = \App\Models\ItWhProjectDocument::where('it_wh_project_id', $project->id)->find($request->document_id);
        }

        if (!$document) {
            $document = new \App\Models\ItWhProjectDocument();
            $document->it_wh_project_id = $project->id;
        }

        $document->type = $request->type;
        $document->description = $request->description;
        $document->document_date = $request->document_date ?: null;
        $document->link = $request->link ?: null;

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($document->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($document->file_path);
            }

            $path = $request->file('file')->store('it-work-hub/documents', 'public');
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
        $document = \App\Models\ItWhProjectDocument::findOrFail($id);
        
        if ($document->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();

        return response()->json(['success' => true, 'message' => 'Dokumen berhasil dihapus.']);
    }

    public function repository()
    {
        return view('it-work-hub.repository');
    }
}
