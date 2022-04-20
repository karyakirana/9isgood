<?php

namespace App\Http\Livewire\Stock\Mutasi;

use Livewire\Component;

class StockMutasiBaikRusakTrans extends Component
{
    protected $listeners = [
        'set_produk'=>'setProduk'
    ];

    // var for manipulate
    public $update = false;
    public $mode = 'create';
    
    public function render()
    {
        return view('livewire.stock.mutasi.stock-mutasi-baik-rusak-trans');
    }
}
