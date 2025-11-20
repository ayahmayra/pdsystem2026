<?php

namespace App\Livewire\SubKeg;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SubKeg;
use App\Models\RekeningBelanja;

class Rekening extends Component
{
    use WithPagination;

    public $subKegId;
    public $subKeg;
    public $search = '';
    public $perPage = 10;

    // Modal properties
    public $showModal = false;
    public $isEditing = false;
    public $editingId = null;

    // Form properties
    public $form = [
        'kode_rekening' => '',
        'nama_rekening' => '',
        'pagu' => '',
        'keterangan' => '',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    protected $rules = [
        'form.kode_rekening' => 'required|string|max:255',
        'form.nama_rekening' => 'required|string|max:255',
        'form.pagu' => 'nullable|numeric|min:0',
        'form.keterangan' => 'nullable|string',
    ];

    public function mount($id)
    {
        $this->subKegId = $id;
        $this->subKeg = SubKeg::with(['unit', 'pptkUser'])->findOrFail($id);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function createRekening()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function editRekening($id)
    {
        $rekening = RekeningBelanja::findOrFail($id);
        
        $this->form = [
            'kode_rekening' => $rekening->kode_rekening,
            'nama_rekening' => $rekening->nama_rekening,
            'pagu' => $rekening->pagu,
            'keterangan' => $rekening->keterangan,
        ];
        
        $this->isEditing = true;
        $this->editingId = $id;
        $this->showModal = true;
    }

    public function storeRekening()
    {
        $this->validate();

        RekeningBelanja::create([
            'sub_keg_id' => $this->subKegId,
            'kode_rekening' => $this->form['kode_rekening'],
            'nama_rekening' => $this->form['nama_rekening'],
            'pagu' => $this->form['pagu'] ?: 0,
            'keterangan' => $this->form['keterangan'],
            'is_active' => true,
        ]);

        $this->closeModal();
        session()->flash('success', 'Rekening belanja berhasil ditambahkan.');
    }

    public function updateRekening()
    {
        $this->validate();

        $rekening = RekeningBelanja::findOrFail($this->editingId);
        $rekening->update([
            'kode_rekening' => $this->form['kode_rekening'],
            'nama_rekening' => $this->form['nama_rekening'],
            'pagu' => $this->form['pagu'] ?: 0,
            'keterangan' => $this->form['keterangan'],
        ]);

        $this->closeModal();
        session()->flash('success', 'Rekening belanja berhasil diupdate.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->isEditing = false;
        $this->editingId = null;
    }

    private function resetForm()
    {
        $this->form = [
            'kode_rekening' => '',
            'nama_rekening' => '',
            'pagu' => '',
            'keterangan' => '',
        ];
    }

    public function render()
    {
        $query = RekeningBelanja::where('sub_keg_id', $this->subKegId)
                                ->where('is_active', true)
                                ->with(['receipts.lines']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kode_rekening', 'like', '%' . $this->search . '%')
                  ->orWhere('nama_rekening', 'like', '%' . $this->search . '%');
            });
        }

        $rekeningBelanja = $query->orderBy('kode_rekening')->paginate($this->perPage);

        return view('livewire.sub-keg.rekening', [
            'rekeningBelanja' => $rekeningBelanja,
        ])->layout('components.layouts.app');
    }
}
