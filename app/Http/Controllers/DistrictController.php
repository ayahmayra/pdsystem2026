<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\City;
use App\Models\Province;
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DistrictController extends Controller
{
    public function index(Request $request)
    {
        // Check if user has permission to view locations
        if (!PermissionHelper::can('locations.view')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat kecamatan.');
        }

        $search = $request->get('search');
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        $districts = District::query()
            ->with(['city.province'])
            ->withCount('orgPlaces')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('kemendagri_code', 'like', '%' . $search . '%')
                      ->orWhereHas('city', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('city.province', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      });
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate(10)
            ->withQueryString();

        return view('districts.index', compact('districts', 'search', 'sortField', 'sortDirection'));
    }

    public function create(Request $request)
    {
        // Check if user has permission to create locations
        if (!PermissionHelper::can('locations.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat kecamatan.');
        }

        $provinces = Province::orderBy('name')->get();
        $cities = collect();
        $selectedProvinceId = $request->get('province_id');
        
        if ($selectedProvinceId) {
            $cities = City::where('province_id', $selectedProvinceId)
                         ->orderBy('name')
                         ->get();
        }

        return view('districts.create', compact('provinces', 'cities', 'selectedProvinceId'));
    }

    public function store(Request $request)
    {
        // Check if user has permission to create locations
        if (!PermissionHelper::can('locations.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat kecamatan.');
        }

        $validated = $request->validate([
            'kemendagri_code' => 'required|string|max:10|unique:districts,kemendagri_code',
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:120',
        ], [
            'kemendagri_code.required' => 'Kode Kemendagri wajib diisi',
            'kemendagri_code.unique' => 'Kode Kemendagri sudah ada',
            'city_id.required' => 'Kota/Kabupaten wajib dipilih',
            'city_id.exists' => 'Kota/Kabupaten yang dipilih tidak valid',
            'name.required' => 'Nama kecamatan wajib diisi',
        ]);

        District::create($validated);

        return redirect()->route('districts.index')
            ->with('message', 'Kecamatan berhasil ditambahkan');
    }

    public function edit(District $district)
    {
        // Check if user has permission to edit locations
        if (!PermissionHelper::can('locations.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit kecamatan.');
        }

        $provinces = Province::orderBy('name')->get();
        $cities = City::where('province_id', $district->city->province_id)
                     ->orderBy('name')
                     ->get();
        $selectedProvinceId = $district->city->province_id;

        return view('districts.edit', compact('district', 'provinces', 'cities', 'selectedProvinceId'));
    }

    public function update(Request $request, District $district)
    {
        // Check if user has permission to edit locations
        if (!PermissionHelper::can('locations.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit kecamatan.');
        }

        $validated = $request->validate([
            'kemendagri_code' => ['required', 'string', 'max:10', Rule::unique('districts')->ignore($district->id)],
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:120',
        ], [
            'kemendagri_code.required' => 'Kode Kemendagri wajib diisi',
            'kemendagri_code.unique' => 'Kode Kemendagri sudah ada',
            'city_id.required' => 'Kota/Kabupaten wajib dipilih',
            'city_id.exists' => 'Kota/Kabupaten yang dipilih tidak valid',
            'name.required' => 'Nama kecamatan wajib diisi',
        ]);

        $district->update($validated);

        return redirect()->route('districts.index')
            ->with('message', 'Kecamatan berhasil diperbarui');
    }

    public function destroy(District $district)
    {
        // Check if user has permission to delete locations
        if (!PermissionHelper::can('locations.delete')) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus kecamatan.');
        }

        // Check if district has org places
        if ($district->orgPlaces()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Kecamatan tidak dapat dihapus karena masih memiliki data kedudukan organisasi');
        }

        $district->delete();

        return redirect()->route('districts.index')
            ->with('message', 'Kecamatan berhasil dihapus');
    }

    public function getCities(Request $request)
    {
        $provinceId = $request->get('province_id');
        
        if (!$provinceId) {
            return response()->json([]);
        }

        $cities = City::where('province_id', $provinceId)
                     ->orderBy('name')
                     ->get(['id', 'name']);

        return response()->json($cities);
    }
}
