<?php

namespace App\Http\Controllers;

use App\Models\DeployRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isProjectManager() || $user->isAdmin()) {
            $stats = [
                'total'    => DeployRequest::count(),
                'pending'  => DeployRequest::where('status', 'pending')->count(),
                'approved' => DeployRequest::where('status', 'approved')->count(),
                'rejected' => DeployRequest::where('status', 'rejected')->count(),
            ];

            $recentRequests = DeployRequest::with(['application', 'requester'])
                ->latest()
                ->take(7)
                ->get();
        } else {
            $stats = [
                'total'    => DeployRequest::where('requester_id', $user->id)->count(),
                'pending'  => DeployRequest::where('requester_id', $user->id)->where('status', 'pending')->count(),
                'approved' => DeployRequest::where('requester_id', $user->id)->where('status', 'approved')->count(),
                'rejected' => DeployRequest::where('requester_id', $user->id)->where('status', 'rejected')->count(),
            ];

            $recentRequests = DeployRequest::with(['application'])
                ->where('requester_id', $user->id)
                ->latest()
                ->take(7)
                ->get();
        }

        return view('dashboard.index', compact('stats', 'recentRequests'));
    }
}
