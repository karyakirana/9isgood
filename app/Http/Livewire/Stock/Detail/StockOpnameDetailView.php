<?php

namespace App\Http\Livewire\Stock\Detail;

use App\Haramain\Traits\LivewireTraits\ResetFormTraits;
use App\Models\Stock\StockOpname;
use Livewire\Component;

class StockOpnameDetailView extends Component
{
    use ResetFormTraits;

    
    public $stock_data, $stock_detail_data;
    
    protected $listeners = [
        'showStockDetail'=>'show'
    ];


    public function render()
    {
        return view('livewire.stock.detail.stock-opname-detail-view');
    }

    public function show(StockOpname $stock_opname)
    {
        $this->stock_data = $stock_opname;
        $this->stock_detail_data = $stock_opname->stockOpnameDetail;
    }
}
