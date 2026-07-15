<?php

namespace App\Http\Controllers;

use App\Models\ItWhRepoType;
use App\Models\ItWhRepoSubType;
use App\Models\ItWhRepoDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItWhRepositoryController extends Controller
{
    public function index()
    {
        $types = ItWhRepoType::with([
            'subTypes.documents',
            'documents'  // direct documents (no sub type)
        ])->orderBy('sort_order')->get();

        return view('it-work-hub.repository.index', compact('types'));
    }

    // ── Jenis ──────────────────────────────────────────────────────────────

    public function storeType(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ItWhRepoType::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'sort_order'  => ItWhRepoType::max('sort_order') + 1,
        ]);

        return redirect()->back()->with('success', 'Jenis berhasil ditambahkan.');
    }

    public function updateType(Request $request, $id)
    {
        $type = ItWhRepoType::findOrFail($id);
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $type->update($validated);
        return redirect()->back()->with('success', 'Jenis berhasil diperbarui.');
    }

    public function destroyType($id)
    {
        $type = ItWhRepoType::findOrFail($id);

        // Delete files from storage
        foreach ($type->allDocuments as $doc) {
            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
        }

        $type->delete(); // cascadeOnDelete handles subTypes & documents

        return redirect()->back()->with('success', 'Jenis dan semua isinya berhasil dihapus.');
    }

    // ── Sub Jenis ──────────────────────────────────────────────────────────

    public function storeSubType(Request $request)
    {
        $validated = $request->validate([
            'it_wh_repo_type_id' => 'required|exists:it_wh_repo_types,id',
            'name'               => 'required|string|max:255',
            'description'        => 'nullable|string',
        ]);

        ItWhRepoSubType::create([
            'it_wh_repo_type_id' => $validated['it_wh_repo_type_id'],
            'name'               => $validated['name'],
            'description'        => $validated['description'] ?? null,
            'sort_order'         => ItWhRepoSubType::where('it_wh_repo_type_id', $validated['it_wh_repo_type_id'])->max('sort_order') + 1,
        ]);

        return redirect()->back()->with('success', 'Sub Jenis berhasil ditambahkan.');
    }

    public function updateSubType(Request $request, $id)
    {
        $subType = ItWhRepoSubType::findOrFail($id);
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $subType->update($validated);
        return redirect()->back()->with('success', 'Sub Jenis berhasil diperbarui.');
    }

    public function destroySubType($id)
    {
        $subType = ItWhRepoSubType::findOrFail($id);

        foreach ($subType->documents as $doc) {
            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
        }

        $subType->delete();

        return redirect()->back()->with('success', 'Sub Jenis dan semua dokumennya berhasil dihapus.');
    }

    // ── Dokumen ────────────────────────────────────────────────────────────

    public function storeDocument(Request $request)
    {
        $validated = $request->validate([
            'it_wh_repo_type_id'     => 'required|exists:it_wh_repo_types,id',
            'it_wh_repo_sub_type_id' => 'nullable|exists:it_wh_repo_sub_types,id',
            'name'                   => 'required|string|max:255',
            'version'                => 'nullable|string|max:50',
            'document_date'          => 'nullable|date',
            'link'                   => 'nullable|url|max:2048',
            'file'                   => 'nullable|file|max:20480', // max 20MB
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('it-work-hub/repository', 'public');
        }

        ItWhRepoDocument::create([
            'it_wh_repo_type_id'     => $validated['it_wh_repo_type_id'],
            'it_wh_repo_sub_type_id' => $validated['it_wh_repo_sub_type_id'] ?? null,
            'name'                   => $validated['name'],
            'version'                => $validated['version'] ?? null,
            'document_date'          => $validated['document_date'] ?? null,
            'link'                   => $validated['link'] ?? null,
            'file_path'              => $filePath,
            'sort_order'             => ItWhRepoDocument::max('sort_order') + 1,
        ]);

        return redirect()->back()->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function updateDocument(Request $request, $id)
    {
        $doc = ItWhRepoDocument::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'version'       => 'nullable|string|max:50',
            'document_date' => 'nullable|date',
            'link'          => 'nullable|url|max:2048',
            'file'          => 'nullable|file|max:20480',
        ]);

        $filePath = $doc->file_path;
        if ($request->hasFile('file')) {
            // Delete old file
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file')->store('it-work-hub/repository', 'public');
        }

        $doc->update([
            'name'          => $validated['name'],
            'version'       => $validated['version'] ?? null,
            'document_date' => $validated['document_date'] ?? null,
            'link'          => $validated['link'] ?? null,
            'file_path'     => $filePath,
        ]);

        return redirect()->back()->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroyDocument($id)
    {
        $doc = ItWhRepoDocument::findOrFail($id);

        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $doc->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
