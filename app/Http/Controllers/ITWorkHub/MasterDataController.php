<?php

namespace App\Http\Controllers\ITWorkHub;

use App\Http\Controllers\Controller;
use App\Models\ItWhMasterStatus;
use App\Models\ItWhMasterDivision;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    // ==========================================
    // STATUSES
    // ==========================================
    public function indexStatuses(Request $request)
    {
        $category = $request->get('category', 'Project App');
        
        $statuses = ItWhMasterStatus::where('category', $category)
            ->orderBy('sort_order')
            ->get();

        return view('it-work-hub.master-data.statuses.index', compact('statuses', 'category'));
    }

    public function storeStatus(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'weight' => 'required|integer',
            'color' => 'nullable|string|max:100',
            'sort_order' => 'required|integer',
        ]);

        ItWhMasterStatus::create([
            'name' => $request->name,
            'category' => $request->category,
            'weight' => $request->weight,
            'color' => $request->color,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('it-work-hub.master-data.statuses.index', ['category' => $request->category])
                         ->with('success', 'Status berhasil ditambahkan.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'weight' => 'required|integer',
            'color' => 'nullable|string|max:100',
            'sort_order' => 'required|integer',
        ]);

        $status = ItWhMasterStatus::findOrFail($id);
        $status->update([
            'name' => $request->name,
            'category' => $request->category,
            'weight' => $request->weight,
            'color' => $request->color,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('it-work-hub.master-data.statuses.index', ['category' => $request->category])
                         ->with('success', 'Status berhasil diperbarui.');
    }

    public function destroyStatus($id)
    {
        $status = ItWhMasterStatus::findOrFail($id);
        $category = $status->category;
        $status->delete();

        return redirect()->route('it-work-hub.master-data.statuses.index', ['category' => $category])
                         ->with('success', 'Status berhasil dihapus.');
    }

    // ==========================================
    // DIVISIONS
    // ==========================================
    public function indexDivisions(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = ItWhMasterDivision::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', $status);
        }

        $divisions = $query->orderBy('name')->paginate(7)->appends($request->all());
        
        return view('it-work-hub.master-data.divisions.index', compact('divisions', 'search', 'status'));
    }

    public function storeDivision(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ItWhMasterDivision::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('it-work-hub.master-data.divisions.index')
                         ->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function updateDivision(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $division = ItWhMasterDivision::findOrFail($id);
        $division->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('it-work-hub.master-data.divisions.index')
                         ->with('success', 'Divisi berhasil diperbarui.');
    }

    public function destroyDivision($id)
    {
        $division = ItWhMasterDivision::findOrFail($id);
        $division->delete();

        return redirect()->route('it-work-hub.master-data.divisions.index')
                         ->with('success', 'Divisi berhasil dihapus.');
    }
}
