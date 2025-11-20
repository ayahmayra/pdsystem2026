<?php

namespace App\Livewire\Ranks;

use App\Models\Rank;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Tambah Pangkat'])]
class Create extends Component
{
    public $code = '';
    public $name = '';

    protected function rules()
    {
        return [
            'code' => 'required|string|max:10|unique:ranks,code',
            'name' => 'required|string|max:255',
        ];
    }

    public function save()
    {
        $validated = $this->validate();
        
        Rank::create($validated);
        
        session()->flash('message', 'Data pangkat berhasil ditambahkan.');
        
        return redirect()->route('ranks.index');
    }

    public function render()
    {
        return view('livewire.ranks.create');
    }
}
