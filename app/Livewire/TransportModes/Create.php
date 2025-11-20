<?php

namespace App\Livewire\TransportModes;

use App\Models\TransportMode;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public $code = '';
    public $name = '';

    protected $rules = [
        'code' => 'required|string|max:20|unique:transport_modes,code',
        'name' => 'required|string|max:100',
    ];

    protected $messages = [
        'code.required' => 'Kode moda transportasi wajib diisi',
        'code.unique' => 'Kode moda transportasi sudah digunakan',
        'code.max' => 'Kode moda transportasi maksimal 20 karakter',
        'name.required' => 'Nama moda transportasi wajib diisi',
        'name.max' => 'Nama moda transportasi maksimal 100 karakter',
    ];

    public function mount()
    {
        // Check if user has permission to create locations
        if (!PermissionHelper::can('locations.create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat moda transportasi.');
        }
    }

    public function save()
    {
        // Check if user has permission to create locations
        if (!PermissionHelper::can('locations.create')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk membuat moda transportasi.');
            return;
        }
        
        $this->validate();

        TransportMode::create([
            'code' => strtoupper($this->code),
            'name' => $this->name,
        ]);

        session()->flash('message', 'Moda transportasi berhasil ditambahkan');
        return redirect()->route('transport-modes.index');
    }

    public function render()
    {
        return view('livewire.transport-modes.create');
    }
}
