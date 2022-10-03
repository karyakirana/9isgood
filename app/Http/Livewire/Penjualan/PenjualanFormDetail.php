<?php

namespace App\Http\Livewire\Penjualan;

use App\Models\Master\Produk;
use Livewire\Component;

class PenjualanFormDetail extends Component
{
    protected $listeners = [
        'setCustomerDiskon',
        'setProduk'
    ];

    public $customer_diskon;
    public $produk_id, $produk_nama, $produk_kode_lokal;
    public $produk_kategori, $produk_kategori_harga, $produk_cover;
    public $harga;
    public $diskon;
    public $sub_total;

    public function setCustomerDiskon($diskon)
    {
        $this->customer_diskon = $diskon;
    }

    protected function resetFormDetail()
    {
        $this->reset([
            'customer_diskon',
            'produk_id', 'produk_nama', 'kode_lokal',
            'harga',
            'diskon',
            'sub_total'
        ]);
    }

    public function setProduk(Produk $produk)
    {
        //
    }

    public function render()
    {
        return view('livewire.penjualan.penjualan-form-detail');
    }
}
