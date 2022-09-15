<?php namespace App\Haramain\SistemStock;

use App\Models\Stock\StockMutasi;

class StockKeluarMutasi extends StockKeluarRepository
{
    public function __construct(StockMutasi $stockMutasi)
    {
        Parent::__construct();
        $this->kondisi = \Str::before($stockMutasi->tgl_mutasi, '_');
        $this->kode = $this->kode($this->kondisi);
        $this->stockableKeluarType = $stockMutasi::class;
        $this->stockableKeluarId = $stockMutasi->id;
        $this->gudangId = $stockMutasi->gudang_asal_id;
        $this->tglKeluar = $stockMutasi->tgl_mutasi;
        $this->userId = $stockMutasi->user_id;
        $this->keterangan = $stockMutasi->keterangan;

        $this->dataDetail = $stockMutasi->stockMutasiDetail;
    }
}
