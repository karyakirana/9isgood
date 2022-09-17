<?php namespace App\Haramain\SistemStock;

use App\Models\Purchase\PembelianRetur;

class StockKeluarPembelianRetur extends StockKeluarRepository
{
    public function __construct(PembelianRetur $pembelianRetur)
    {
        parent::__construct();
        $this->kode = $this->kode($pembelianRetur->kondisi);
        $this->activeCash = $pembelianRetur->active_cash;
        $this->stockableKeluarType = $pembelianRetur::class;
        $this->stockableKeluarId = $pembelianRetur->id;
        $this->kondisi = $pembelianRetur->kondisi;
        $this->gudangId = $pembelianRetur->gudang_id;
        $this->supplierId = $pembelianRetur->supplier_id;
        $this->tglKeluar = $pembelianRetur->tgl_nota;
        $this->userId = $pembelianRetur->user_id;
        $this->keterangan = $pembelianRetur->keterangan;

        $this->dataDetail = $pembelianRetur->returDetail;
    }
}
