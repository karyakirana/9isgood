<?php

namespace App\Http\Livewire\Z;

use App\Haramain\Repository\Persediaan\PersediaanRepository;
use App\Models\Keuangan\Persediaan;
use App\Models\Penjualan\Penjualan;
use App\Models\Penjualan\PenjualanRetur;
use App\Models\Purchase\Pembelian;
use App\Models\Purchase\PembelianRetur;
use App\Models\Stock\StockOpname;
use Livewire\Component;

class Tester extends Component
{
    public $stockData, $produk_id;
    public function mount()
    {
        $this->stockData = $this->queryMe();
    }

    public function queryMe()
    {
        return (new PersediaanRepository())->getProdukForKeluar(302, 2, 500);
    }

    public function render()
    {
        return view('livewire.z.tester');
    }
}
