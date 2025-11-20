<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Province;
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    public function index(Request $request)
    {
        // Check if user has permission to view locations
        if (!PermissionHelper::can('locations.view')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat kota/kabupaten.');
        }

        $search = $request->get('search');
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        $cities = City::query()
            ->with(['province'])
            ->withCount(['districts', 'orgPlaces'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('kemendagri_code', 'like', '%' . $search . '%')
                      ->orWhereHas('province', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      });
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate(10)
            ->withQueryString();

        return view('cities.index', compact('cities', 'search', 'sortField', 'sortDirection'));
    }

    public function create()
    {
        // Check if user has permission to create locations
        if (!PermissionHelper::can('locations.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat kota/kabupaten.');
        }

        $provinces = Province::orderBy('name')->get();
        return view('cities.create', compact('provinces'));
    }

    public function store(Request $request)
    {
        // Check if user has permission to create locations
        if (!PermissionHelper::can('locations.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat kota/kabupaten.');
        }

        $validated = $request->validate([
            'kemendagri_code' => 'required|string|max:10|unique:cities,kemendagri_code',
            'province_id' => 'required|exists:provinces,id',
            'name' => 'required|string|max:120',
            'type' => 'required|in:KAB,KOTA',
        ], [
            'kemendagri_code.required' => 'Kode Kemendagri wajib diisi',
            'kemendagri_code.unique' => 'Kode Kemendagri sudah ada',
            'province_id.required' => 'Provinsi wajib dipilih',
            'province_id.exists' => 'Provinsi yang dipilih tidak valid',
            'name.required' => 'Nama kota/kabupaten wajib diisi',
            'type.required' => 'Tipe wajib dipilih',
            'type.in' => 'Tipe harus KAB atau KOTA',
        ]);

        City::create($validated);

        return redirect()->route('cities.index')
            ->with('message', 'Kota/Kabupaten berhasil ditambahkan');
    }

    public function edit(City $city)
    {
        // Check if user has permission to edit locations
        if (!PermissionHelper::can('locations.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit kota/kabupaten.');
        }

        $provinces = Province::orderBy('name')->get();
        return view('cities.edit', compact('city', 'provinces'));
    }

    public function update(Request $request, City $city)
    {
        // Check if user has permission to edit locations
        if (!PermissionHelper::can('locations.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit kota/kabupaten.');
        }

        $validated = $request->validate([
            'kemendagri_code' => ['required', 'string', 'max:10', Rule::unique('cities')->ignore($city->id)],
            'province_id' => 'required|exists:provinces,id',
            'name' => 'required|string|max:120',
            'type' => 'required|in:KAB,KOTA',
        ], [
            'kemendagri_code.required' => 'Kode Kemendagri wajib diisi',
            'kemendagri_code.unique' => 'Kode Kemendagri sudah ada',
            'province_id.required' => 'Provinsi wajib dipilih',
            'province_id.exists' => 'Provinsi yang dipilih tidak valid',
            'name.required' => 'Nama kota/kabupaten wajib diisi',
            'type.required' => 'Tipe wajib dipilih',
            'type.in' => 'Tipe harus KAB atau KOTA',
        ]);

        $city->update($validated);

        return redirect()->route('cities.index')
            ->with('message', 'Kota/Kabupaten berhasil diperbarui');
    }

    public function destroy(City $city)
    {
        // Check if user has permission to delete locations
        if (!PermissionHelper::can('locations.delete')) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus kota/kabupaten.');
        }

        // Check if city has districts
        if ($city->districts()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Kota/Kabupaten tidak dapat dihapus karena masih memiliki data kecamatan');
        }

        // Check if city has org places
        if ($city->orgPlaces()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Kota/Kabupaten tidak dapat dihapus karena masih memiliki data kedudukan organisasi');
        }

        $city->delete();

        return redirect()->route('cities.index')
            ->with('message', 'Kota/Kabupaten berhasil dihapus');
    }
}
