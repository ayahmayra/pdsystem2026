<?php

namespace App\Livewire\PerdiemRates;

use App\Models\PerdiemRate;
use App\Models\Province;
use App\Models\TravelGrade;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public $province_id = '';
    public $satuan = 'OH';
    public $luar_kota = '';
    public $dalam_kota_gt8h = '';
    public $diklat = '';

    protected $rules = [
        'province_id' => 'required|exists:provinces,id',
        'satuan' => 'required|string|max:10',
        'luar_kota' => 'required|numeric|min:0',
        'dalam_kota_gt8h' => 'required|numeric|min:0',
        'diklat' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'province_id.required' => 'Provinsi wajib dipilih',
        'province_id.exists' => 'Provinsi yang dipilih tidak valid',
        'satuan.required' => 'Satuan wajib diisi',
        'satuan.max' => 'Satuan maksimal 10 karakter',
        'luar_kota.required' => 'Tarif luar kota wajib diisi',
        'luar_kota.numeric' => 'Tarif luar kota harus berupa angka',
        'luar_kota.min' => 'Tarif luar kota minimal 0',
        'dalam_kota_gt8h.required' => 'Tarif dalam kota >8 jam wajib diisi',
        'dalam_kota_gt8h.numeric' => 'Tarif dalam kota >8 jam harus berupa angka',
        'dalam_kota_gt8h.min' => 'Tarif dalam kota >8 jam minimal 0',
        'diklat.required' => 'Tarif diklat wajib diisi',
        'diklat.numeric' => 'Tarif diklat harus berupa angka',
        'diklat.min' => 'Tarif diklat minimal 0',
    ];

    public function mount()
    {
        // Check if user has permission to create reference rates
        if (!PermissionHelper::can('reference-rates.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat tarif uang harian.');
        }
    }

    public function save()
    {
        // Check if user has permission to create reference rates
        if (!PermissionHelper::can('reference-rates.create')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk membuat tarif uang harian.');
            return;
        }
        
        $this->validate();

        // Check for duplicate province
        $existingRate = PerdiemRate::where('province_id', $this->province_id)->first();

        if ($existingRate) {
            session()->flash('error', 'Tarif untuk provinsi ini sudah ada');
            return;
        }

        PerdiemRate::create([
            'province_id' => $this->province_id,
            'satuan' => $this->satuan,
            'luar_kota' => $this->luar_kota,
            'dalam_kota_gt8h' => $this->dalam_kota_gt8h,
            'diklat' => $this->diklat,
        ]);

        session()->flash('message', 'Tarif uang harian berhasil ditambahkan');
        return redirect()->route('perdiem-rates.index');
    }

    public function render()
    {
        $provinces = Province::orderBy('name')->get();

        return view('livewire.perdiem-rates.create', compact('provinces'));
    }
}
