<?php namespace App\Haramain\SistemStock;

use App\Models\Penjualan\PenjualanRetur;

class StockMasukPenjualanRetur extends StockMasukRepository
{
    public function __construct(PenjualanRetur $penjualanRetur)
    {
        $this->kode = $this->kode($penjualanRetur->jenis_retur);
        $this->activeCash = session('ClosedCash');
        $this->stockableType = $penjualanRetur::class;
        $this->stockableId = $penjualanRetur->id;
        $this->kondisi = $penjualanRetur->jenis_retur;
        $this->gudangId = $penjualanRetur->gudang_id;
        $this->supplierId = null;
        $this->tglMasuk = $penjualanRetur->tgl_nota;
        $this->userId = $penjualanRetur->user_id;
        $this->nomorPo = '-';
        $this->nomorSuratJalan = '-';
        $this->keterangan = $penjualanRetur->keterangan;

        $this->dataDetail = $penjualanRetur->returDetail;
    }

    public static function build(PenjualanRetur $penjualanRetur)
    {
        return new static($penjualanRetur);
    }

    public function rollback()
    {
        foreach ($this->dataDetail as $item) {
            StockInventoryRepository::build($this->kondisi, $this->gudangId, $item)->rollback('stock_masuk');
        }
        return $this->getDataByStockable()->stockMasukDetail()->delete();
    }
}
