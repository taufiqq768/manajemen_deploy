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
            'version_api_write' => 'nullable|url|max:255',
            'version_api_write_key' => 'nullable|string|max:100',
            'version_api_write_notes_key' => 'nullable|string|max:100',
        ]);

        $application->update($validated);

        $message = 'Konfigurasi API Versi Write untuk ' . $application->name . ' berhasil diperbarui.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'version' => $application->version
            ]);
        }

        return redirect()->route('applications.index')->with('success', $message);
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

    public function getVersion(Request $request)
    {
        $query = Application::query();

        if ($request->has('api_id')) {
            $query->where('api_id', $request->api_id);
        } elseif ($request->has('name')) {
            $query->where('name', $request->name);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Parameter "name" atau "api_id" wajib diisi.'
            ], 400);
        }

        $application = $query->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Aplikasi tidak ditemukan.'
            ], 404);
        }

        // Retrieve latest approved deploy request for release notes
        $latestApproved = \App\Models\DeployRequest::where('application_id', $application->id)
            ->where('status', 'approved')
            ->where('version', $application->version)
            ->latest('approved_at')
            ->first();

        $changelog = null;
        if ($latestApproved) {
            $releaseNotesRaw = $latestApproved->release_notes;
            if (is_array($releaseNotesRaw)) {
                $lines = [];
                if (!empty($releaseNotesRaw['perubahan_besar'])) {
                    $lines[] = "Perubahan Besar:\n" . $releaseNotesRaw['perubahan_besar'];
                }
                if (!empty($releaseNotesRaw['perubahan_kecil'])) {
                    $lines[] = "Perubahan Kecil:\n" . $releaseNotesRaw['perubahan_kecil'];
                }
                if (!empty($releaseNotesRaw['bug_fixing'])) {
                    $lines[] = "Bug Fixing:\n" . $releaseNotesRaw['bug_fixing'];
                }
                $changelog = implode("\n\n", $lines);
            } else {
                $changelog = (string) $releaseNotesRaw;
            }
        }

        return response()->json([
            'success' => true,
            'application' => $application->name,
            'version' => $application->version ?? '0.0.0',
            'changelog' => $changelog,
            'updated_at' => $application->updated_at ? $application->updated_at->toIso8601String() : null
        ]);
    }

    public function updateVersionManual(Request $request, Application $application)
    {
        $validated = $request->validate([
            'version' => 'required|string|max:50',
            'release_notes' => 'nullable|string|max:1000',
        ]);

        $application->update(['version' => $validated['version']]);

        $message = 'Versi aplikasi ' . $application->name . ' berhasil diperbarui secara manual ke ' . $application->version;

        if ($application->version_api_write) {
            $pushResult = $application->pushVersionToRemote($validated['version'], $validated['release_notes']);
            if ($pushResult['success']) {
                $message .= ' dan sukses dikirim ke remote server.';
            } else {
                $message .= ', namun GAGAL mengirim ke remote server: ' . $pushResult['message'];
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'version' => $application->version
            ]);
        }

        return redirect()->route('applications.index')->with('success', $message);
    }
}
