<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Echelon;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $positions = Position::with('echelon')
            ->withCount('users')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('type', 'like', '%' . $search . '%')
                      ->orWhereHas('echelon', function ($eq) use ($search) {
                          $eq->where('name', 'like', '%' . $search . '%')
                             ->orWhere('code', 'like', '%' . $search . '%');
                      });
                });
            })
            ->leftJoin('echelons', 'positions.echelon_id', '=', 'echelons.id')
            // 1. Sort by eselon (lower number = higher eselon)
            ->orderByRaw('CASE WHEN echelons.id IS NULL THEN 999999 ELSE echelons.id END ASC')
            // 2. Sort by position name
            ->orderBy('positions.name')
            ->select('positions.*')
            ->paginate(10)
            ->withQueryString();

        return view('positions.index', compact('positions', 'search'));
    }

    public function create()
    {
        $echelons = Echelon::orderBy('code', 'asc')->get(); // I.a (tertinggi) first
        
        return view('positions.create', compact('echelons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'echelon_id' => 'nullable|exists:echelons,id',
        ], [
            'name.required' => 'Nama jabatan wajib diisi',
            'echelon_id.exists' => 'Eselon yang dipilih tidak valid',
        ]);

        // Convert empty string to null for echelon_id
        if (isset($validated['echelon_id']) && $validated['echelon_id'] === '') {
            $validated['echelon_id'] = null;
        }

        Position::create($validated);

        return redirect()->route('positions.index')
            ->with('message', 'Data jabatan berhasil ditambahkan.');
    }

    public function edit(Position $position)
    {
        $echelons = Echelon::orderBy('code', 'asc')->get(); // I.a (tertinggi) first
        
        return view('positions.edit', compact('position', 'echelons'));
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'echelon_id' => 'nullable|exists:echelons,id',
        ], [
            'name.required' => 'Nama jabatan wajib diisi',
            'echelon_id.exists' => 'Eselon yang dipilih tidak valid',
        ]);

        // Convert empty string to null for echelon_id
        if (isset($validated['echelon_id']) && $validated['echelon_id'] === '') {
            $validated['echelon_id'] = null;
        }

        $position->update($validated);

        return redirect()->route('positions.index')
            ->with('message', 'Data jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position)
    {
        try {
            // Check if position is being used by users
            if ($position->users()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Jabatan tidak dapat dihapus karena masih digunakan oleh pegawai.');
            }

            $position->delete();

            return redirect()->route('positions.index')
                ->with('message', 'Data jabatan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data jabatan. ' . $e->getMessage());
        }
    }
}
