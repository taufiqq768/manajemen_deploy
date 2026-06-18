<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WahaConnectionController extends Controller
{
    public function index()
    {
        $wahaUrl = rtrim(config('services.waha.url'), '/');
        $wahaSession = config('services.waha.session', 'default');
        $wahaApiKey = config('services.waha.api_key');

        $status = 'unknown';
        $message = 'Checking connection...';
        $sessionData = null;

        if (empty($wahaUrl)) {
            $status = 'error';
            $message = 'WAHA URL is not configured in .env';
            return view('waha-connection.index', compact('status', 'message', 'sessionData', 'wahaUrl', 'wahaSession'));
        }

        try {
            $headers = ['Accept' => 'application/json'];
            if ($wahaApiKey) {
                $headers['X-Api-Key'] = $wahaApiKey;
            }

            // Check session status
            $response = Http::withHeaders($headers)
                ->timeout(15)
                ->get("{$wahaUrl}/api/sessions", [
                    'session' => $wahaSession,
                ]);

            if ($response->successful()) {
                $sessions = $response->json();
                
                // WAHA /api/sessions usually returns an array of sessions
                $currentSession = collect($sessions)->firstWhere('name', $wahaSession);
                
                if ($currentSession) {
                    $status = $currentSession['status'] ?? 'unknown';
                    $message = "Session '{$wahaSession}' retrieved successfully.";
                    $sessionData = $currentSession;
                } else {
                    $status = 'not_found';
                    $message = "Session '{$wahaSession}' not found in WAHA.";
                }
            } else {
                $status = 'error';
                $message = "Error fetching sessions. HTTP Status: " . $response->status() . ". " . $response->body();
            }
        } catch (\Exception $e) {
            $status = 'error';
            $message = "Connection error: " . $e->getMessage();
            Log::error('[WAHA Check] Connection error: ' . $e->getMessage());
        }

        $logs = \App\Models\WahaConnectionLog::where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'asc')
            ->get();

        $uptimePercentage = 100;
        if ($logs->count() > 0) {
            $workingCount = $logs->where('status', 'WORKING')->count();
            $uptimePercentage = round(($workingCount / $logs->count()) * 100, 2);
        }

        // Format data for ApexCharts
        // Format: [ timestamp, status_code (1 for WORKING, 0 for others) ]
        $chartData = $logs->map(function ($log) {
            return [
                $log->created_at->timestamp * 1000,
                $log->status === 'WORKING' ? 1 : 0
            ];
        })->values()->toArray();

        // If no logs exist yet, prepend a data point from 24 hours ago 
        // so the chart can draw a line instead of being empty.
        if (count($chartData) === 0) {
            $chartData[] = [
                now()->subHours(24)->timestamp * 1000,
                $status === 'WORKING' ? 1 : 0
            ];
        }

        // Append the current live status to the chart so it extends to "now"
        $chartData[] = [
            now()->timestamp * 1000,
            $status === 'WORKING' ? 1 : 0
        ];

        return view('waha-connection.index', compact('status', 'message', 'sessionData', 'wahaUrl', 'wahaSession', 'logs', 'uptimePercentage', 'chartData'));
    }
}
