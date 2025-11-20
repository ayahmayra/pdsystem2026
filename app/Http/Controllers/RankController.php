<?php

namespace App\Http\Controllers;

use App\Models\Rank;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RankController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $ranks = Rank::withCount('users')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', '%' . $search . '%')
                      ->orWhere('name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('code', 'desc') // Pangkat tertinggi (IV/e) first
            ->paginate(10)
            ->withQueryString();

        return view('ranks.index', compact('ranks', 'search'));
    }

    public function create()
    {
        return view('ranks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:ranks,code',
            'name' => 'required|string|max:255',
        ], [
            'code.required' => 'Kode pangkat wajib diisi',
            'code.unique' => 'Kode pangkat sudah ada',
            'name.required' => 'Nama pangkat wajib diisi',
        ]);

        Rank::create($validated);

        return redirect()->route('ranks.index')
            ->with('message', 'Data pangkat berhasil ditambahkan.');
    }

    public function edit(Rank $rank)
    {
        return view('ranks.edit', compact('rank'));
    }

    public function update(Request $request, Rank $rank)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', Rule::unique('ranks')->ignore($rank->id)],
            'name' => 'required|string|max:255',
        ], [
            'code.required' => 'Kode pangkat wajib diisi',
            'code.unique' => 'Kode pangkat sudah ada',
            'name.required' => 'Nama pangkat wajib diisi',
        ]);

        $rank->update($validated);

        return redirect()->route('ranks.index')
            ->with('message', 'Data pangkat berhasil diperbarui.');
    }

    public function destroy(Rank $rank)
    {
        try {
            // Check if rank is being used by users
            if ($rank->users()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Pangkat tidak dapat dihapus karena masih digunakan oleh pegawai.');
            }

            $rank->delete();

            return redirect()->route('ranks.index')
                ->with('message', 'Data pangkat berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data pangkat. ' . $e->getMessage());
        }
    }
}
