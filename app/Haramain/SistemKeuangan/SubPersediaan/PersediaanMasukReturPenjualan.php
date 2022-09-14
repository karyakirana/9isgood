<?php namespace App\Haramain\SistemKeuangan\SubPersediaan;

use App\Models\Keuangan\HargaHppALL;
use App\Models\Keuangan\Persediaan;
use App\Models\Master\Produk;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PersediaanMasukReturPenjualan extends PersediaanMasuk
{
    protected $produkNama;
    protected $produkKodeLokal;
    protected $hpp;

    public function __construct($gudangId, $kondisi, $tglInput, $produkId, $harga, $jumlah)
    {
        parent::__construct($gudangId, $kondisi, $tglInput, $produkId, $harga, $jumlah);
        $produk = Produk::find($this->produkId);
        $this->produkNama = $produk->nama;
        $this->produkKodeLokal = $produk->kode_lokal;
        $this->hpp = HargaHppALL::find(1)->persen;
        $this->getProdukLatest($harga);
    }

    private function getProdukLatest($harga)
    {
        $persediaanLatest = Persediaan::query()
            ->where('gudang_id', $this->gudangId)
            ->where('jenis', $this->kondisi)
            ->where('produk_id', $this->produkId)
            ->latest('tgl_input');
        if ($persediaanLatest->exists()){
            $persediaan = $persediaanLatest->first();
            $this->harga = $persediaan->harga;
            // throw new ModelNotFoundException("Data {$this->produkKodeLokal} {$this->produkNama} belum diinputkan pada persediaan");
        } else {
            $this->harga = (float) $this->hpp * $harga;
        }
    }
}
