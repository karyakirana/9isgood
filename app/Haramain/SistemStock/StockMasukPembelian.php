<?php namespace App\Haramain\SistemStock;

use App\Models\Purchase\Pembelian;

class StockMasukPembelian extends StockMasukRepository
{
    public function __construct(Pembelian $pembelian, $dataTambahan = null)
    {
        $this->kode = $this->kode('baik');
        $this->activeCash = session('ClosedCash');
        $this->stockableType = $pembelian::class;
        $this->stockableId = $pembelian->id;
        $this->kondisi = 'baik';
        $this->gudangId = $pembelian->gudang_id;
        $this->supplierId = $pembelian->supplier_id;
        $this->tglMasuk = $pembelian->tgl_nota;
        $this->userId = $pembelian->user_id;
        $this->keterangan = $pembelian->keterangan;

        if ($dataTambahan){
            $this->nomorPo = $dataTambahan['nomorPo'];
            $this->nomorSuratJalan = $dataTambahan['suratJalan'];
        }

        $this->dataDetail = $pembelian->pembelianDetail;
    }
}
