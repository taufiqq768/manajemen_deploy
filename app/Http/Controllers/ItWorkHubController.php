<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItWorkHubController extends Controller
{
    public function dashboard()
    {
        // App Dev Stats
        $allAppDevStatuses = ['Not Started', 'Development', 'Live', 'Live w/ CR', 'Live w/ Bug', 'Hold', 'Retired'];
        $appDevRaw = \App\Models\ItWhProject::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
        $appDevStats = collect($allAppDevStatuses)->mapWithKeys(fn($s) => [$s => $appDevRaw[$s] ?? 0]);
        $appDevTotal = (int) $appDevStats->sum();
        $appDevAvgProgress = round(\App\Models\ItWhProject::avg('progress') ?? 0);
        $appDevPriorityRaw = \App\Models\ItWhProject::selectRaw('priority, count(*) as count')->groupBy('priority')->pluck('count', 'priority');
        $appDevPriorityStats = ['High' => $appDevPriorityRaw['High'] ?? 0, 'Medium' => $appDevPriorityRaw['Medium'] ?? 0, 'Low' => $appDevPriorityRaw['Low'] ?? 0];

        // Non App Stats
        $allNonAppStatuses = ['Not Started', 'Development', 'Live', 'Live w/ CR', 'Live w/ Bug', 'Hold', 'Retired'];
        $nonAppRaw = \App\Models\ItWhNonappProject::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
        $nonAppStats = collect($allNonAppStatuses)->mapWithKeys(fn($s) => [$s => $nonAppRaw[$s] ?? 0]);
        $nonAppTotal = (int) $nonAppStats->sum();
        $nonAppAvgProgress = round(\App\Models\ItWhNonappProject::avg('progress') ?? 0);
        $nonAppPriorityRaw = \App\Models\ItWhNonappProject::selectRaw('priority, count(*) as count')->groupBy('priority')->pluck('count', 'priority');
        $nonAppPriorityStats = ['High' => $nonAppPriorityRaw['High'] ?? 0, 'Medium' => $nonAppPriorityRaw['Medium'] ?? 0, 'Low' => $nonAppPriorityRaw['Low'] ?? 0];

        // Governance Stats
        $governanceAll = \App\Models\ItWhGovernance::selectRaw('progress, priority')->get();
        $governanceStats = ['Not Started' => 0, 'On Progress' => 0, 'Done' => 0];
        $governancePriorityStats = ['High' => 0, 'Medium' => 0, 'Low' => 0];
        foreach ($governanceAll as $gov) {
            if ($gov->progress == 0) $governanceStats['Not Started']++;
            elseif ($gov->progress == 100) $governanceStats['Done']++;
            else $governanceStats['On Progress']++;
            if (isset($governancePriorityStats[$gov->priority])) $governancePriorityStats[$gov->priority]++;
        }
        $governanceTotal = $governanceAll->count();
        $governanceAvgProgress = round($governanceAll->avg('progress') ?? 0);

        // Project Group Stats
        $allGroupStatuses = ['Not Started', 'Progress', 'Live', 'Live w/ CR', 'Live (Bug Fixing)', 'Hold', 'Retired'];
        $groupRaw = \App\Models\ItWhProjectGroup::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
        $groupStats = collect($allGroupStatuses)->mapWithKeys(fn($s) => [$s => $groupRaw[$s] ?? 0]);
        $groupTotal = (int) $groupStats->sum();
        $groupAvgProgress = round(\App\Models\ItWhProjectGroup::avg('progress') ?? 0);

        // PIC Stats: combine activities from all 3 modules per user
        $appDevActByPic = \Illuminate\Support\Facades\DB::table('it_wh_activity_user')
            ->join('users', 'users.id', '=', 'it_wh_activity_user.user_id')
            ->join('it_wh_activities', 'it_wh_activities.id', '=', 'it_wh_activity_user.it_wh_activity_id')
            ->selectRaw('users.id as user_id, users.name, it_wh_activities.status, count(*) as count')
            ->groupBy('users.id', 'users.name', 'it_wh_activities.status')
            ->get();

        $nonAppActByPic = \Illuminate\Support\Facades\DB::table('it_wh_nonapp_activity_user')
            ->join('users', 'users.id', '=', 'it_wh_nonapp_activity_user.user_id')
            ->join('it_wh_nonapp_activities', 'it_wh_nonapp_activities.id', '=', 'it_wh_nonapp_activity_user.it_wh_nonapp_activity_id')
            ->selectRaw('users.id as user_id, users.name, it_wh_nonapp_activities.status, count(*) as count')
            ->groupBy('users.id', 'users.name', 'it_wh_nonapp_activities.status')
            ->get();

        $govActByPic = \Illuminate\Support\Facades\DB::table('it_wh_governance_activity_user')
            ->join('users', 'users.id', '=', 'it_wh_governance_activity_user.user_id')
            ->join('it_wh_governance_activities', 'it_wh_governance_activities.id', '=', 'it_wh_governance_activity_user.it_wh_governance_activity_id')
            ->selectRaw('users.id as user_id, users.name, it_wh_governance_activities.status, count(*) as count')
            ->groupBy('users.id', 'users.name', 'it_wh_governance_activities.status')
            ->get();

        $picSummary = collect();
        foreach ([$appDevActByPic, $nonAppActByPic, $govActByPic] as $dataset) {
            foreach ($dataset as $row) {
                $key = $row->user_id;
                if (!$picSummary->has($key)) {
                    $picSummary->put($key, ['name' => $row->name, 'total' => 0, 'done' => 0]);
                }
                $entry = $picSummary->get($key);
                $entry['total'] += $row->count;
                if ($row->status === 'Done') $entry['done'] += $row->count;
                $picSummary->put($key, $entry);
            }
        }
        $picSummary = $picSummary->sortByDesc('total')->values();
        $picNames = $picSummary->pluck('name')->toArray();
        $picTotals = $picSummary->pluck('total')->toArray();
        $picDone = $picSummary->pluck('done')->toArray();
        $picProgress = $picSummary->map(fn($p) => $p['total'] > 0 ? round(($p['done'] / $p['total']) * 100) : 0)->toArray();

        // Total Activities & Overdue
        $overdueAppDev = \App\Models\ItWhActivity::where('status', '!=', 'Done')
            ->whereRaw('COALESCE(adjustment_date, deadline) < ?', [today()])->count();
        $overdueNonApp = \App\Models\ItWhNonappActivity::where('status', '!=', 'Done')
            ->whereRaw('COALESCE(adjustment_date, deadline) < ?', [today()])->count();
        $overdueGov = \App\Models\ItWhGovernanceActivity::where('status', '!=', 'Done')
            ->whereRaw('COALESCE(adjustment_date, deadline) < ?', [today()])->count();

        $totalAppDevAct = \App\Models\ItWhActivity::count();
        $totalNonAppAct = \App\Models\ItWhNonappActivity::count();
        $totalGovAct = \App\Models\ItWhGovernanceActivity::count();

        $totalActivities = $totalAppDevAct + $totalNonAppAct + $totalGovAct;
        $totalOverdue = $overdueAppDev + $overdueNonApp + $overdueGov;

        return view('it-work-hub.dashboard', compact(
            'appDevStats', 'appDevTotal', 'appDevAvgProgress', 'appDevPriorityStats',
            'nonAppStats', 'nonAppTotal', 'nonAppAvgProgress', 'nonAppPriorityStats',
            'governanceStats', 'governancePriorityStats', 'governanceTotal', 'governanceAvgProgress',
            'groupStats', 'groupTotal', 'groupAvgProgress',
            'picSummary', 'picNames', 'picTotals', 'picDone', 'picProgress',
            'totalAppDevAct', 'totalNonAppAct', 'totalGovAct', 'totalActivities',
            'overdueAppDev', 'overdueNonApp', 'overdueGov', 'totalOverdue'
        ));
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
            'development' => \App\Models\ItWhProject::where('status', 'Development')->count(),
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

    public function overdueActivities()
    {
        $today = today();
        
        // 1. App Dev Activities
        $appDev = \App\Models\ItWhActivity::with('project')
            ->where('status', '!=', 'Done')
            ->whereRaw('COALESCE(adjustment_date, deadline) < ?', [$today])
            ->get()
            ->map(function ($act) use ($today) {
                $dueDate = $act->adjustment_date ? \Carbon\Carbon::parse($act->adjustment_date) : \Carbon\Carbon::parse($act->deadline);
                return (object) [
                    'name' => $act->name,
                    'feature' => 'App Dev',
                    'parent_name' => $act->project ? $act->project->name : '-',
                    'due_date' => $dueDate,
                    'days_overdue' => $dueDate->diffInDays($today, false),
                    'action_url' => $act->project ? route('it-work-hub.activities', $act->project->id) : '#'
                ];
            });

        // 2. Non App Activities
        $nonApp = \App\Models\ItWhNonappActivity::with('project')
            ->where('status', '!=', 'Done')
            ->whereRaw('COALESCE(adjustment_date, deadline) < ?', [$today])
            ->get()
            ->map(function ($act) use ($today) {
                $dueDate = $act->adjustment_date ? \Carbon\Carbon::parse($act->adjustment_date) : \Carbon\Carbon::parse($act->deadline);
                return (object) [
                    'name' => $act->name,
                    'feature' => 'Non App',
                    'parent_name' => $act->project ? $act->project->name : '-',
                    'due_date' => $dueDate,
                    'days_overdue' => $dueDate->diffInDays($today, false),
                    'action_url' => $act->project ? route('it-work-hub.non-app.activities', $act->project->id) : '#'
                ];
            });

        // 3. Governance Activities
        $governance = \App\Models\ItWhGovernanceActivity::with('governance')
            ->where('status', '!=', 'Done')
            ->whereRaw('COALESCE(adjustment_date, deadline) < ?', [$today])
            ->get()
            ->map(function ($act) use ($today) {
                $dueDate = $act->adjustment_date ? \Carbon\Carbon::parse($act->adjustment_date) : \Carbon\Carbon::parse($act->deadline);
                return (object) [
                    'name' => $act->name,
                    'feature' => 'Governance',
                    'parent_name' => $act->governance ? $act->governance->name : '-',
                    'due_date' => $dueDate,
                    'days_overdue' => $dueDate->diffInDays($today, false),
                    'action_url' => $act->governance ? route('it-work-hub.governance.activities', $act->governance->id) : '#'
                ];
            });

        // Merge and sort
        $overdueActivities = collect()
            ->merge($appDev)
            ->merge($nonApp)
            ->merge($governance)
            ->sortByDesc('days_overdue')
            ->values();

        return view('it-work-hub.overdue-activities', compact('overdueActivities'));
    }
}
