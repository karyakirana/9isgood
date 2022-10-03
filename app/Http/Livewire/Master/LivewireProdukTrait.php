<?php namespace App\Http\Livewire\Master;

use App\Models\Master\Produk;

trait LivewireProdukTrait
{
    public $produk_id, $kode_lokal;
    public $produk_nama;
    public $diskon;
    public $harga, $harga_setelah_diskon;
    public $harga_rupiah;

    public function setProduk(Produk $produk)
    {
        if(method_exists($this, 'resetFormDetailAttribute')){
            $this->resetFormDetailAttribute();
        }
        $this->produk_id = $produk->id;
        $this->kode_lokal = $produk->kode_lokal;
        $this->produk_nama = $produk->nama."\n".$produk->kode_lokal."\n".$produk->kategoriHarga->deskripsi."\n".$produk->cover;
        $this->harga = $produk->harga;
        $this->harga_rupiah = rupiah_format($produk->harga);
        $this->diskon = property_exists($this, 'customer_diskon') ? $this->customer_diskon : 0;
        $this->harga_setelah_diskon = ($this->diskon != 0 && $this->diskon) ?
            (float)$this->harga - (float) ($this->harga * ((float)$this->diskon / 100)) :
            null;
    }
}
