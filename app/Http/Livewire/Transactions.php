<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Transactions extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.transactions')
            ->with('transactions', Auth::user()->transactions()->paginate(50));
    }
}
