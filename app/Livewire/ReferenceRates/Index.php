<?php

namespace App\Livewire\ReferenceRates;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.reference-rates.index');
    }
}
