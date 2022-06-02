<?php

namespace App\Http\Livewire\Keuangan\Neraca;

use App\Models\Keuangan\NeracaSaldo;
use Livewire\Component;

class NeracaSaldoIndex extends Component
{
    public function render()
    {
        return view('livewire.keuangan.neraca.neraca-saldo-index');
    }
 
    public function pipe()
    {
        $piped = $collection->pipe(function ($collection) {
            $collection = collect(['debet']);
            return $collection->sum();
            });        
    }
}
