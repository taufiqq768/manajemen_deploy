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

        return view('it-work-hub.app-dev.longlist', compact('projects', 'stats'));
    }

    public function create()
    {
        // Ambil data user selain admin dan project_manager untuk Squad
        $users = \App\Models\User::whereNotIn('role', ['admin', 'project_manager'])->get();

        return view('it-work-hub.app-dev.create', compact('users'));
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
        $users = \App\Models\User::whereNotIn('role', ['admin', 'project_manager'])->get();
        return view('it-work-hub.app-dev.show', compact('project', 'users'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'squads' => 'nullable|array',
            'squads.*' => 'exists:users,id',
            'bpo' => 'nullable|string|max:255',
            'pain_point_uraian' => 'nullable|string',
            'pain_point_impact' => 'nullable|string',
            'priority' => 'required|in:High,Medium,Low',
        ]);

        $project = \App\Models\ItWhProject::findOrFail($id);
        $project->update([
            'bpo' => $validated['bpo'] ?? null,
            'pain_point_uraian' => $validated['pain_point_uraian'] ?? null,
            'pain_point_impact' => $validated['pain_point_impact'] ?? null,
            'priority' => $validated['priority'],
        ]);

        if (isset($validated['squads'])) {
            $project->squads()->sync($validated['squads']);
        } else {
            $project->squads()->detach();
        }

        return redirect()->back()->with('success', 'Informasi project berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $project = \App\Models\ItWhProject::findOrFail($id);
        
        // Hapus relasi untuk menghindari foreign key constraint error
        $project->squads()->detach();
        $project->groups()->detach();
        $project->activities()->delete();
        $project->documents()->delete();
        
        $project->delete();

        return redirect()->route('it-work-hub.longlist')->with('success', 'Project berhasil dihapus.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $project = \App\Models\ItWhProject::findOrFail($id);
        $project->status = $request->status;
        $project->save();

        return response()->json(['success' => true, 'message' => 'Status project berhasil diperbarui.']);
    }

    public function activities($id)
    {
        $project = \App\Models\ItWhProject::with(['squads', 'activities.pics'])->findOrFail($id);
        $users = \App\Models\User::whereNotIn('role', ['admin', 'project_manager'])->get();
        return view('it-work-hub.app-dev.activities', compact('project', 'users'));
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
            'activities.*.deadline' => 'nullable|date',
            'activities.*.adjustment_date' => 'nullable|date',
            'activities.*.notes' => 'nullable|string',
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
                    'deadline' => $actData['deadline'] ?: null,
                    'adjustment_date' => $actData['adjustment_date'] ?: null,
                    'notes' => $actData['notes'] ?? null,
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

        // Project progress is automatically recalculated via Model Events (booted in ItWhActivity)

        $project->refresh();

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

    public function projectGroups()
    {
        $projectGroups = \App\Models\ItWhProjectGroup::with('projects')->orderBy('sort_order')->get();
        $projects = \App\Models\ItWhProject::orderBy('name')->get();
        
        $stats = [
            'total' => \App\Models\ItWhProjectGroup::count(),
            'not_started' => \App\Models\ItWhProjectGroup::where('status', 'Not Started')->count(),
            'progress' => \App\Models\ItWhProjectGroup::where('status', 'Progress')->count(),
            'live' => \App\Models\ItWhProjectGroup::where('status', 'Live')->count(),
            'live_cr' => \App\Models\ItWhProjectGroup::where('status', 'Live w/ CR')->count(),
            'live_bug' => \App\Models\ItWhProjectGroup::where('status', 'Live (Bug Fixing)')->count(),
            'hold' => \App\Models\ItWhProjectGroup::whereIn('status', ['Hold', 'Retired'])->count(),
        ];

        return view('it-work-hub.project-groups', compact('projectGroups', 'projects', 'stats'));
    }

    public function updateProjectGroups(Request $request)
    {
        $request->validate([
            'groups' => 'present|array',
            'groups.*.id' => 'nullable',
            'groups.*.name' => 'required|string',
            'groups.*.status' => 'required|string',
            'groups.*.deadline' => 'nullable|date',
            'groups.*.description' => 'nullable|string',
            'groups.*.sort_order' => 'required|integer',
            'groups.*.projects' => 'nullable|array',
            'groups.*.projects.*' => 'exists:it_wh_projects,id',
        ]);

        $incomingIds = [];
        $groupsData = $request->input('groups', []);

        foreach ($groupsData as $groupData) {
            $group = \App\Models\ItWhProjectGroup::updateOrCreate(
                ['id' => (isset($groupData['id']) && is_numeric($groupData['id'])) ? $groupData['id'] : null],
                [
                    'name' => $groupData['name'],
                    'status' => $groupData['status'],
                    'deadline' => $groupData['deadline'] ?: null,
                    'description' => $groupData['description'] ?? null,
                    'sort_order' => $groupData['sort_order'],
                ]
            );

            if (isset($groupData['projects'])) {
                $group->projects()->sync($groupData['projects']);
            } else {
                $group->projects()->detach();
            }

            // Calculate progress based on attached projects
            $attachedProjects = $group->projects()->get();
            if ($attachedProjects->count() > 0) {
                $group->progress = round($attachedProjects->avg('progress'));
            } else {
                $group->progress = 0;
            }
            $group->save();

            $incomingIds[] = $group->id;
        }

        // Delete groups that are no longer in the payload
        \App\Models\ItWhProjectGroup::whereNotIn('id', $incomingIds)->delete();

        return response()->json(['success' => true, 'message' => 'Project Groups berhasil disimpan.']);
    }
    public function todo()
    {
        $user = auth()->user();
        
        $query = \App\Models\ItWhTodo::with(['user', 'assigner']);
        
        // Project Managers and Admins can see all tasks. Ordinary users see their own.
        if (!in_array($user->role, ['admin', 'project_manager'])) {
            $query->where('user_id', $user->id);
        }

        $todos = $query->orderBy('sort_order', 'asc')->get();
        $users = \App\Models\User::orderBy('name')->get();

        return view('it-work-hub.todo', compact('todos', 'users'));
    }

    public function updateTodos(Request $request)
    {
        $user = auth()->user();
        $isAdminOrPM = in_array($user->role, ['admin', 'project_manager']);

        $request->validate([
            'todos' => 'present|array',
            'todos.*.id' => 'nullable',
            'todos.*.user_id' => 'required|exists:users,id',
            'todos.*.date' => 'required|date',
            'todos.*.task_name' => 'required|string|max:255',
            'todos.*.deadline' => 'required|date',
            'todos.*.status' => 'required|in:To Do,In Progress,Done',
            'todos.*.notes' => 'nullable|string',
            'todos.*.sort_order' => 'required|integer',
        ]);

        $incomingIds = [];
        $todosData = $request->input('todos', []);

        foreach ($todosData as $todoData) {
            // Ordinary users can only assign to themselves
            if (!$isAdminOrPM) {
                $todoData['user_id'] = $user->id;
            }

            $todoId = (isset($todoData['id']) && is_numeric($todoData['id'])) ? $todoData['id'] : null;

            if ($todoId) {
                $todo = \App\Models\ItWhTodo::find($todoId);
                // Security check for non-admins
                if (!$isAdminOrPM && $todo && $todo->user_id != $user->id) {
                    continue; // Skip if trying to edit someone else's task
                }
            }

            $todo = \App\Models\ItWhTodo::updateOrCreate(
                ['id' => $todoId],
                [
                    'user_id' => $todoData['user_id'],
                    'assigner_id' => $todoId ? (\App\Models\ItWhTodo::find($todoId)->assigner_id ?? $user->id) : $user->id,
                    'date' => $todoData['date'],
                    'task_name' => $todoData['task_name'],
                    'deadline' => $todoData['deadline'],
                    'status' => $todoData['status'],
                    'notes' => $todoData['notes'] ?? null,
                    'sort_order' => $todoData['sort_order'],
                ]
            );

            $incomingIds[] = $todo->id;
        }

        // Delete todos that are no longer in the payload
        $deleteQuery = \App\Models\ItWhTodo::whereNotIn('id', $incomingIds);
        if (!$isAdminOrPM) {
            $deleteQuery->where('user_id', $user->id); // Ordinary users only delete their own
        }
        $deleteQuery->delete();

        return response()->json(['success' => true, 'message' => 'To-Do List berhasil disimpan.']);
    }
}
