<?php

namespace App\Http\Livewire\Penjualan;

use App\Haramain\Traits\LivewireTraits\ResetFormTraits;
use App\Models\Penjualan\PenjualanRetur;
use Livewire\Component;

class PenjualanReturDetailView extends Component
{
    use ResetFormTraits;

    protected $listeners = [
        'showPenjualanReturDetail'=>'show'
    ];

    public $penjualan_data, $penjualan_detail_data;

    public function render()
    {
        return view('livewire.penjualan.penjualan-retur-detail-view');
    }

    public function show(PenjualanRetur $penjualanRetur)
    {
        $this->penjualan_data = $penjualanRetur;
        $this->penjualan_detail_data = $penjualanRetur->returDetail;
    }
}
