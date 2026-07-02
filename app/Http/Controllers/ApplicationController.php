<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use App\Services\AppSyncService;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct(protected AppSyncService $appSync)
    {
    }

    public function index()
    {
        // Load data dari database lokal beserta relasi pics
        $applications = Application::with('pics')->orderBy('name')->paginate(15);
        
        $programmers = User::where('role', 'programmer')->get();

        return view('applications.index', compact('applications', 'programmers'));
    }

    public function sync()
    {
        $this->appSync->sync();
        return redirect()->route('applications.index')->with('success', 'Data aplikasi berhasil disinkronkan dari GUP API.');
    }

    public function create()
    {
        $programmers = User::where('role', 'programmer')->get();
        return view('applications.create', compact('programmers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'description'       => 'nullable|string',
            'repo_url'          => 'nullable|url|max:255',
            'app_url'           => 'nullable|url|max:255',
            'version'           => 'nullable|string|max:50',
            'version_api_get'   => 'nullable|url|max:255',
            'version_api_write' => 'nullable|url|max:255',
            'version_api_key'   => 'nullable|string|max:100',
            'version_api_write_key' => 'nullable|string|max:100',
            'version_api_write_notes_key' => 'nullable|string|max:100',
            'pic_ids'           => 'nullable|array',
            'pic_ids.*'         => 'exists:users,id',
        ]);

        $application = Application::create($validated);
        $application->pics()->sync($request->input('pic_ids', []));

        return redirect()->route('applications.index')
            ->with('success', 'Aplikasi berhasil ditambahkan.');
    }

    public function edit(Application $application)
    {
        $programmers = User::where('role', 'programmer')->get();
        return view('applications.edit', compact('application', 'programmers'));
    }

    public function update(Request $request, Application $application)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'description'       => 'nullable|string',
            'repo_url'          => 'nullable|url|max:255',
            'app_url'           => 'nullable|url|max:255',
            'version'           => 'nullable|string|max:50',
            'version_api_get'   => 'nullable|url|max:255',
            'version_api_write' => 'nullable|url|max:255',
            'version_api_key'   => 'nullable|string|max:100',
            'version_api_write_key' => 'nullable|string|max:100',
            'version_api_write_notes_key' => 'nullable|string|max:100',
            'pic_ids'           => 'nullable|array',
            'pic_ids.*'         => 'exists:users,id',
        ]);

        $application->update($validated);
        $application->pics()->sync($request->input('pic_ids', []));

        return redirect()->route('applications.index')
            ->with('success', 'Aplikasi berhasil diperbarui.');
    }

    public function updateVersionApi(Request $request, Application $application)
    {
        $validated = $request->validate([
            'version_api_get'   => 'nullable|url|max:255',
            'version_api_write' => 'nullable|url|max:255',
            'version_api_key'   => 'nullable|string|max:100',
            'version_api_write_key' => 'nullable|string|max:100',
            'version_api_write_notes_key' => 'nullable|string|max:100',
        ]);

        $application->update($validated);

        // Fetch version immediately if version_api_get is configured
        if ($application->version_api_get) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(3)->get($application->version_api_get);
                if ($response->successful()) {
                    $version = null;
                    $contentType = $response->header('Content-Type');
                    if (str_contains($contentType, 'application/json') || is_array($response->json())) {
                        $data = $response->json();
                        $keyPath = $application->version_api_key ?: 'version';
                        $version = data_get($data, $keyPath);
                        if ($version === null && !$application->version_api_key) {
                            $version = data_get($data, 'version') ?: data_get($data, 'versi') ?: data_get($data, 'data.version');
                        }
                    } else {
                        $version = trim($response->body());
                    }

                    if ($version !== null) {
                        if (is_array($version) || is_object($version)) {
                            $version = json_encode($version);
                        }
                        $version = strip_tags((string) $version);
                        $version = substr(trim($version), 0, 50);
                        if ($version !== '') {
                            $application->update(['version' => $version]);
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Gagal fetch versi saat update API untuk {$application->name}: " . $e->getMessage());
            }
        }

        $message = 'Konfigurasi API Versi untuk ' . $application->name . ' berhasil diperbarui.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'version' => $application->version
            ]);
        }

        return redirect()->route('applications.index')->with('success', $message);
    }

    public function refreshVersions(Request $request)
    {
        $applications = Application::whereNotNull('version_api_get')->get();
        
        $successCount = 0;
        $failCount = 0;
        $details = [];

        foreach ($applications as $app) {
            $oldVersion = $app->version;
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(3)->get($app->version_api_get);
                
                if ($response->successful()) {
                    $version = null;
                    
                    // Cek jika response berbentuk JSON
                    $contentType = $response->header('Content-Type');
                    if (str_contains($contentType, 'application/json') || is_array($response->json())) {
                        $data = $response->json();
                        // Gunakan key custom yang diatur oleh user (mendukung dot-notation seperti data.no_versi)
                        $keyPath = $app->version_api_key ?: 'version';
                        $version = data_get($data, $keyPath);
                        
                        // Jika key custom tidak ditemukan, coba cari key-key umum
                        if ($version === null && !$app->version_api_key) {
                            $version = data_get($data, 'version') ?: data_get($data, 'versi') ?: data_get($data, 'data.version');
                        }

                        if ($version === null) {
                            $failCount++;
                            $details[] = "{$app->name}: Key JSON tidak cocok/ditemukan";
                            
                            \App\Models\VersionLog::create([
                                'application_id' => $app->id,
                                'type' => 'sync',
                                'old_version' => $oldVersion,
                                'new_version' => null,
                                'status' => 'failed',
                                'message' => "Gagal refresh versi: Key JSON tidak cocok/ditemukan",
                                'created_at' => now(),
                            ]);
                            continue;
                        }
                    } else {
                        $version = trim($response->body());
                    }
                    
                    // Cek jika null/object/array karena json decode bermasalah
                    if (is_array($version) || is_object($version)) {
                        $version = json_encode($version);
                    }

                    // Bersihkan tag html/whitespace jika ada
                    $version = strip_tags((string) $version);
                    $version = substr(trim($version), 0, 50);

                    if ($version !== '') {
                        // Jika versi remote lebih rendah dari versi sistem, push versi sistem ke remote
                        if ($oldVersion && version_compare($version, $oldVersion) < 0) {
                            $pushResult = $app->pushVersionToRemote($oldVersion, "Sinkronisasi otomatis: Versi di server remote ({$version}) lebih rendah dari sistem ({$oldVersion}).");
                            if ($pushResult['success']) {
                                $successCount++;
                            } else {
                                $failCount++;
                                $details[] = "{$app->name}: Gagal push update ({$pushResult['message']})";
                            }
                        } else {
                            // Versi remote sama atau lebih tinggi, update versi sistem dengan versi remote
                            $app->update(['version' => $version]);
                            $successCount++;
                            
                            \App\Models\VersionLog::create([
                                'application_id' => $app->id,
                                'type' => 'sync',
                                'old_version' => $oldVersion,
                                'new_version' => $version,
                                'status' => 'success',
                                'message' => 'Berhasil mensinkronisasi versi dari API Get.',
                                'created_at' => now(),
                            ]);
                        }
                    } else {
                        $failCount++;
                        $details[] = "{$app->name}: Respon kosong";
                        
                        \App\Models\VersionLog::create([
                            'application_id' => $app->id,
                            'type' => 'sync',
                            'old_version' => $oldVersion,
                            'new_version' => null,
                            'status' => 'failed',
                            'message' => "Gagal refresh versi: Respon kosong",
                            'created_at' => now(),
                        ]);
                    }
                } else {
                    $failCount++;
                    $details[] = "{$app->name}: HTTP {$response->status()}";
                    
                    \App\Models\VersionLog::create([
                        'application_id' => $app->id,
                        'type' => 'sync',
                        'old_version' => $oldVersion,
                        'new_version' => null,
                        'status' => 'failed',
                        'message' => "Gagal refresh versi: Respon HTTP {$response->status()}",
                        'created_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                $failCount++;
                $details[] = "{$app->name}: Koneksi gagal";
                \Illuminate\Support\Facades\Log::warning("Gagal fetch versi untuk {$app->name}: " . $e->getMessage());
                
                \App\Models\VersionLog::create([
                    'application_id' => $app->id,
                    'type' => 'sync',
                    'old_version' => $oldVersion,
                    'new_version' => null,
                    'status' => 'failed',
                    'message' => "Gagal refresh versi (Error Koneksi): " . $e->getMessage(),
                    'created_at' => now(),
                ]);
            }
        }

        $message = "Pembaruan versi selesai. Berhasil: {$successCount}, Gagal: {$failCount}.";
        if (!empty($details)) {
            $message .= " (" . implode(', ', $details) . ")";
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'success_count' => $successCount,
                'fail_count' => $failCount
            ]);
        }

        return redirect()->route('applications.index')->with('success', $message);
    }

    public function testVersionApi(Request $request)
    {
        $validated = $request->validate([
            'version_api_get' => 'required|url|max:255',
            'version_api_key' => 'nullable|string|max:100',
        ]);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get($validated['version_api_get']);
            
            if ($response->successful()) {
                $version = null;
                $contentType = $response->header('Content-Type');
                $isJson = str_contains($contentType, 'application/json') || is_array($response->json());
                
                if ($isJson) {
                    $data = $response->json();
                    $keyPath = $validated['version_api_key'] ?: 'version';
                    $version = data_get($data, $keyPath);
                    
                    if ($version === null && !$validated['version_api_key']) {
                        $version = data_get($data, 'version') ?: data_get($data, 'versi') ?: data_get($data, 'data.version');
                    }

                    if ($version === null) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Respon berupa JSON, tetapi key field "' . ($validated['version_api_key'] ?: 'version') . '" tidak ditemukan atau nilainya kosong.'
                        ]);
                    }
                } else {
                    $version = trim($response->body());
                }

                if (is_array($version) || is_object($version)) {
                    $version = json_encode($version);
                }

                $version = strip_tags((string) $version);
                $version = substr(trim($version), 0, 50);

                if ($version !== '') {
                    return response()->json([
                        'success' => true,
                        'version' => $version
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal membaca versi dari respon API (respon kosong).'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'API merespon dengan status code ' . $response->status()
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error koneksi: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy(Application $application)
    {
        $application->delete();

        return redirect()->route('applications.index')
            ->with('success', 'Aplikasi berhasil dihapus.');
    }

    public function pushVersion(Application $application)
    {
        $result = $application->pushVersionToRemote();

        if ($result['success']) {
            return redirect()->route('applications.index')
                ->with('success', "Sukses push versi: {$result['message']}");
        } else {
            return redirect()->route('applications.index')
                ->with('error', "Gagal push versi: {$result['message']}");
        }
    }
}
