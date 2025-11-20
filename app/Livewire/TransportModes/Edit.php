<?php

namespace App\Livewire\TransportModes;

use App\Models\TransportMode;
use App\Helpers\PermissionHelper;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public TransportMode $transportMode;
    public $code = '';
    public $name = '';

    protected $rules = [
        'code' => 'required|string|max:20',
        'name' => 'required|string|max:100',
    ];

    protected $messages = [
        'code.required' => 'Kode moda transportasi wajib diisi',
        'code.max' => 'Kode moda transportasi maksimal 20 karakter',
        'name.required' => 'Nama moda transportasi wajib diisi',
        'name.max' => 'Nama moda transportasi maksimal 100 karakter',
    ];

    public function mount(TransportMode $transportMode)
    {
        // Check if user has permission to edit locations
        if (!PermissionHelper::can('locations.edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit moda transportasi.');
        }
        
        $this->transportMode = $transportMode;
        $this->code = $transportMode->code;
        $this->name = $transportMode->name;
    }

    public function update()
    {
        // Check if user has permission to edit locations
        if (!PermissionHelper::can('locations.edit')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengedit moda transportasi.');
            return;
        }
        
        $this->validate([
            'code' => 'required|string|max:20|unique:transport_modes,code,' . $this->transportMode->id,
            'name' => 'required|string|max:100',
        ]);

        $this->transportMode->update([
            'code' => strtoupper($this->code),
            'name' => $this->name,
        ]);

        session()->flash('message', 'Moda transportasi berhasil diperbarui');
        return redirect()->route('transport-modes.index');
    }

    public function render()
    {
        return view('livewire.transport-modes.edit');
    }
}
