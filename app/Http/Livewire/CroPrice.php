<?php

namespace App\Http\Livewire;

use App\Coingecko;
use Livewire\Component;

class CroPrice extends Component
{
    public function render()
    {
        return view('livewire.cro-price')
            ->with('price', Coingecko::price('cro', 'eur'));
    }
}
