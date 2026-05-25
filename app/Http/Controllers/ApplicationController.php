<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        // Tidak load 'pic' lagi karena kolom dihapus
        $applications = Application::orderBy('name')->paginate(15);

        return view('applications.index', compact('applications'));
    }

    public function create()
    {
        return view('applications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'repo_url'    => 'nullable|url|max:255',
        ]);

        Application::create($validated);

        return redirect()->route('applications.index')
            ->with('success', 'Aplikasi berhasil ditambahkan.');
    }

    public function edit(Application $application)
    {
        return view('applications.edit', compact('application'));
    }

    public function update(Request $request, Application $application)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'repo_url'    => 'nullable|url|max:255',
        ]);

        $application->update($validated);

        return redirect()->route('applications.index')
            ->with('success', 'Aplikasi berhasil diperbarui.');
    }

    public function destroy(Application $application)
    {
        $application->delete();

        return redirect()->route('applications.index')
            ->with('success', 'Aplikasi berhasil dihapus.');
    }
}
