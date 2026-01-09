<?php

namespace App\Http\Controllers;

use App\Models\Instansi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InstansiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $instansis = Instansi::withCount('users')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', '%' . $search . '%')
                      ->orWhere('name', 'like', '%' . $search . '%')
                      ->orWhere('address', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('instansis.index', compact('instansis', 'search'));
    }

    public function create()
    {
        return view('instansis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:50|unique:instansis,code',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
        ], [
            'code.unique' => 'Kode instansi sudah ada',
            'name.required' => 'Nama instansi wajib diisi',
        ]);

        Instansi::create($validated);

        return redirect()->route('instansis.index')
            ->with('message', 'Data instansi berhasil ditambahkan.');
    }

    public function edit(Instansi $instansi)
    {
        return view('instansis.edit', compact('instansi'));
    }

    public function update(Request $request, Instansi $instansi)
    {
        $validated = $request->validate([
            'code' => ['nullable', 'string', 'max:50', Rule::unique('instansis')->ignore($instansi->id)],
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
        ], [
            'code.unique' => 'Kode instansi sudah ada',
            'name.required' => 'Nama instansi wajib diisi',
        ]);

        $instansi->update($validated);

        return redirect()->route('instansis.index')
            ->with('message', 'Data instansi berhasil diperbarui.');
    }

    public function destroy(Instansi $instansi)
    {
        try {
            // Check if instansi is being used by users
            if ($instansi->users()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Instansi tidak dapat dihapus karena masih digunakan oleh pegawai.');
            }

            $instansi->delete();

            return redirect()->route('instansis.index')
                ->with('message', 'Data instansi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data instansi. ' . $e->getMessage());
        }
    }
}
