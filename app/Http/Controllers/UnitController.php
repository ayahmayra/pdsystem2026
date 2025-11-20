<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $units = Unit::with(['parent', 'children'])
            ->withCount(['users', 'children'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', '%' . $search . '%')
                      ->orWhere('name', 'like', '%' . $search . '%')
                      ->orWhereHas('parent', function ($pq) use ($search) {
                          $pq->where('name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('units.index', compact('units', 'search'));
    }

    public function create()
    {
        $units = Unit::orderBy('code')->get(); // For parent dropdown
        
        return view('units.create', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:units,code',
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:units,id',
        ], [
            'code.required' => 'Kode unit wajib diisi',
            'code.unique' => 'Kode unit sudah ada',
            'name.required' => 'Nama unit wajib diisi',
            'parent_id.exists' => 'Unit parent yang dipilih tidak valid',
        ]);

        // Convert empty string to null for parent_id
        if (isset($validated['parent_id']) && $validated['parent_id'] === '') {
            $validated['parent_id'] = null;
        }

        Unit::create($validated);

        return redirect()->route('units.index')
            ->with('message', 'Data unit berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        // Exclude current unit and its descendants from parent options
        $units = Unit::where('id', '!=', $unit->id)
            ->orderBy('code')
            ->get()
            ->filter(function ($potentialParent) use ($unit) {
                return !$this->isDescendant($potentialParent, $unit);
            });

        return view('units.edit', compact('unit', 'units'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('units')->ignore($unit->id)],
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:units,id',
        ], [
            'code.required' => 'Kode unit wajib diisi',
            'code.unique' => 'Kode unit sudah ada',
            'name.required' => 'Nama unit wajib diisi',
            'parent_id.exists' => 'Unit parent yang dipilih tidak valid',
        ]);

        // Prevent self-reference
        if ($validated['parent_id'] == $unit->id) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Unit tidak dapat menjadi parent dari dirinya sendiri.');
        }

        // Convert empty string to null for parent_id
        if (isset($validated['parent_id']) && $validated['parent_id'] === '') {
            $validated['parent_id'] = null;
        }

        $unit->update($validated);

        return redirect()->route('units.index')
            ->with('message', 'Data unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        try {
            // Check if unit is being used by users
            if ($unit->users()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Unit tidak dapat dihapus karena masih digunakan oleh pegawai.');
            }

            // Check if unit has children
            if ($unit->children()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Unit tidak dapat dihapus karena masih memiliki sub unit.');
            }

            $unit->delete();

            return redirect()->route('units.index')
                ->with('message', 'Data unit berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data unit. ' . $e->getMessage());
        }
    }

    /**
     * Check if a unit is a descendant of the ancestor unit
     */
    private function isDescendant(Unit $potentialDescendant, Unit $ancestor): bool
    {
        $current = $potentialDescendant;
        while ($current->parent_id) {
            if ($current->parent_id == $ancestor->id) {
                return true;
            }
            $current = $current->parent;
        }
        return false;
    }
}
