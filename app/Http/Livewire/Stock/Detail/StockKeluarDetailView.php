<?php

namespace App\Http\Livewire\Stock\Detail;

use App\Haramain\Traits\LivewireTraits\ResetFormTraits;
use App\Models\Stock\StockKeluar;
use Livewire\Component;

class StockKeluarDetailView extends Component
{ 
    use ResetFormTraits;

    protected $listeners = [
        'showStockDetail'=>'show'
    ];

    public $stock_data, $stock_detail_data;

    public function render()
    {
        return view('livewire.stock.detail.stock-keluar-detail-view');
    }
    
    public function show(StockKeluar $stock_keluar)
    {
        $this->stock_data = $stock_keluar;
        $this->stock_detail_data = $stock_keluar->stockKeluarDetail;
    }
}
