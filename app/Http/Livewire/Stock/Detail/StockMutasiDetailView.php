<?php

namespace App\Http\Livewire\Stock\Detail;

use App\Haramain\Traits\LivewireTraits\ResetFormTraits;
use App\Models\Stock\StockMutasi;
use Livewire\Component;

class StockMutasiDetailView extends Component
{
    use ResetFormTraits;

    protected $listeners = [
        'showStockDetail'=>'show'
    ];

    public function render()
    {
        return view('livewire.stock.detail.stock-mutasi-detail-view');
    }

    public function show(StockMutasi $stock_mutasi)
    {
        $this->stock_data = $stock_mutasi;
        $this->stock_detail_data = $stock_mutasi->stockMutasiDetail;
    }
}
