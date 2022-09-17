<?php namespace App\Http\Livewire\Master;

use App\Models\Master\Produk;
use App\Models\Master\Supplier;

trait SetMasterTrait
{
    // produk attribute
    public $produk_id, $produk_nama, $produk_kode_lokal, $produk_harga;

    public function setProduk(Produk $produk)
    {
        $this->produk_id = $produk->id;
        $this->produk_nama = $produk->nama."\n".$produk->kategoriHarga->nama."\n".$produk->cover;
        $this->produk_kode_lokal = $produk->kode_lokal;
        $this->produk_harga = $produk->harga;
    }

    // supplier attribute
    public $supplierId, $supplierNama;

    public function setSupplier(Supplier $supplier)
    {
        $this->supplierId = $supplier->id;
        $this->supplierNama = $supplier->nama;
    }
}
