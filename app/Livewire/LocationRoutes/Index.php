<?php

namespace App\Livewire\LocationRoutes;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.location-routes.index');
    }
}
