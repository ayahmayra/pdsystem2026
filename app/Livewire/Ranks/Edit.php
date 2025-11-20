<?php

namespace App\Livewire\Ranks;

use App\Models\Rank;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Edit Pangkat'])]
class Edit extends Component
{
    public Rank $rank;
    public $code = '';
    public $name = '';

    public function mount(Rank $rank)
    {
        $this->rank = $rank;
        $this->code = $rank->code;
        $this->name = $rank->name;
    }

    protected function rules()
    {
        return [
            'code' => 'required|string|max:10|unique:ranks,code,' . $this->rank->id,
            'name' => 'required|string|max:255',
        ];
    }

    public function update()
    {
        $validated = $this->validate();
        
        $this->rank->update($validated);
        
        session()->flash('message', 'Data pangkat berhasil diperbarui.');
        
        return redirect()->route('ranks.index');
    }

    public function render()
    {
        return view('livewire.ranks.edit');
    }
}
