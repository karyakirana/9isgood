<?php

namespace App\Http\Livewire\Stock;

use Livewire\Component;
use Str;

class StockMutasiIndex extends Component
{
    public $jenisMutasi;

    public function mount()
    {
        $this->jenisMutasi = match (Str::afterLast(url()->current(), '/')) {
            "baik_baik" => "baik_baik",
            "baik_rusak" => "baik_rusak",
            "rusak_rusak" => "rusak_rusak",
            default => null,
        };
    }

    public function render()
    {
        return view('livewire.stock.stock-mutasi-index');
    }
}
