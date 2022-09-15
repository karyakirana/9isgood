<?php namespace App\Haramain\SistemStock;

use App\Models\Stock\StockMutasi;
use Str;

class StockMasukMutasi extends StockMasukRepository
{
    public function __construct(StockMutasi $stockMutasi)
    {
        $this->kondisi = Str::after($stockMutasi->jenis_mutasi, '_');
        $this->kode = $this->kode($this->kondisi);
        $this->activeCash = session('ClosedCash');
        $this->stockableType = $stockMutasi::class;
        $this->stockableId = $stockMutasi->id;
        $this->gudangId = $stockMutasi->gudang_tujuan_id;
        $this->supplierId = null;
        $this->tglMasuk = $stockMutasi->tgl_mutasi;
        $this->userId = $stockMutasi->user_id;
        $this->nomorPo = '-';
        $this->nomorSuratJalan = '-';
        $this->keterangan = $stockMutasi->keterangan;

        $this->dataDetail = $stockMutasi->stockMutasiDetail;
    }
}
