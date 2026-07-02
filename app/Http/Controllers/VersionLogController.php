<?php

namespace App\Http\Controllers;

use App\Models\VersionLog;
use Illuminate\Http\Request;

class VersionLogController extends Controller
{
    public function index(Request $request)
    {
        $query = VersionLog::with('application')->orderBy('created_at', 'desc');

        if ($request->filled('application_id')) {
            $query->where('application_id', $request->application_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->paginate(20)->withQueryString();
        $applications = \App\Models\Application::orderBy('name')->get();

        return view('version-logs.index', compact('logs', 'applications'));
    }
}
