<?php

namespace App\Http\Livewire\Purchase;

use App\Models\Purchase\PembelianRetur;
use Livewire\Component;

class PembelianReturDetailView extends Component
{
    protected $listeners = [
        'showPembelianReturDetail'
    ];

    public $pembelianRetur;
    public $pembelianReturDetail;

    public function showPembelianReturDetail(PembelianRetur $pembelianRetur)
    {
        $this->pembelianRetur = $pembelianRetur;
        $this->pembelianReturDetail = $pembelianRetur->returDetail;
    }

    public function render()
    {
        return view('livewire.purchase.pembelian-retur-detail-view');
    }
}
