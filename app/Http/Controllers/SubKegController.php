<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubKeg;
use App\Models\Unit;

class SubKegController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('sub-keg.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sub-keg.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_subkeg' => 'required|string|max:255|unique:sub_keg,kode_subkeg',
            'nama_subkeg' => 'required|string|max:255',
            'pagu' => 'nullable|numeric|min:0',
            'id_unit' => 'required|exists:units,id',
        ]);

        SubKeg::create($validated);

        return redirect()->route('sub-keg.index')
            ->with('success', 'Sub Kegiatan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubKeg $subKeg)
    {
        return view('sub-keg.show', compact('subKeg'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubKeg $subKeg)
    {
        return view('sub-keg.edit', compact('subKeg'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubKeg $subKeg)
    {
        $validated = $request->validate([
            'kode_subkeg' => 'required|string|max:255|unique:sub_keg,kode_subkeg,' . $subKeg->id,
            'nama_subkeg' => 'required|string|max:255',
            'pagu' => 'nullable|numeric|min:0',
            'id_unit' => 'required|exists:units,id',
        ]);

        $subKeg->update($validated);

        return redirect()->route('sub-keg.index')
            ->with('success', 'Sub Kegiatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubKeg $subKeg)
    {
        $subKeg->delete();

        return redirect()->route('sub-keg.index')
            ->with('success', 'Sub Kegiatan berhasil dihapus.');
    }
}
