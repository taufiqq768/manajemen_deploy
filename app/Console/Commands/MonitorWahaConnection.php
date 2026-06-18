<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WahaConnectionLog;

class MonitorWahaConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waha:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pings WAHA API and logs the connection status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $wahaUrl = rtrim(config('services.waha.url'), '/');
        $wahaSession = config('services.waha.session', 'default');
        $wahaApiKey = config('services.waha.api_key');

        if (empty($wahaUrl)) {
            $this->error('WAHA URL is not configured.');
            return;
        }

        $startTime = microtime(true);
        $status = 'error';
        $errorMessage = null;

        try {
            $headers = ['Accept' => 'application/json'];
            if ($wahaApiKey) {
                $headers['X-Api-Key'] = $wahaApiKey;
            }

            $response = Http::withHeaders($headers)
                ->timeout(15)
                ->get("{$wahaUrl}/api/sessions", [
                    'session' => $wahaSession,
                ]);

            if ($response->successful()) {
                $sessions = $response->json();
                $currentSession = collect($sessions)->firstWhere('name', $wahaSession);
                
                if ($currentSession) {
                    $status = $currentSession['status'] ?? 'unknown';
                } else {
                    $status = 'not_found';
                    $errorMessage = "Session '{$wahaSession}' not found.";
                }
            } else {
                $errorMessage = "HTTP Error: " . $response->status() . " " . $response->body();
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error('[WAHA Monitor] ' . $errorMessage);
        }

        $responseTimeMs = (int) round((microtime(true) - $startTime) * 1000);

        // Prevent spam: only alert if previous state was WORKING (or null) and current state is NOT WORKING
        if ($status !== 'WORKING') {
            $lastLog = WahaConnectionLog::latest()->first();
            if (!$lastLog || $lastLog->status === 'WORKING') {
                $admins = \App\Models\User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($admin->email)
                            ->send(new \App\Mail\WahaDownAlertMail($status, $errorMessage, $wahaUrl));
                    } catch (\Exception $mailEx) {
                        Log::error('[WAHA Monitor] Failed to send down alert email: ' . $mailEx->getMessage());
                    }
                }
            }
        }

        WahaConnectionLog::create([
            'status' => $status,
            'response_time_ms' => $responseTimeMs,
            'error_message' => $errorMessage,
        ]);

        $this->info("WAHA connection checked. Status: {$status}, Response Time: {$responseTimeMs}ms");
    }
}
