<?php namespace App\Haramain\SistemStock;


use App\Models\Penjualan\Penjualan;

class StockKeluarPenjualan extends StockKeluarRepository
{
    public function __construct(Penjualan $penjualan)
    {
        parent::__construct();
        $this->kode = $this->kode('baik');
        $this->supllierId = null;
        $this->stockableKeluarType = $penjualan::class;
        $this->stockableKeluarId = $penjualan->id;
        $this->kondisi = 'baik';
        $this->gudangId = $penjualan->gudang_id;
        $this->tglKeluar = $penjualan->tgl_nota;
        $this->userId = $penjualan->user_id;
        $this->keterangan = $penjualan->keterangan;

        $this->dataDetail = $penjualan->penjualanDetail;
    }

    public static function build(Penjualan $penjualan)
    {
        return new static($penjualan);
    }

    public function rollback()
    {
        $stockKeluar = $this->getDataByStockable();
        foreach ($this->dataDetail as $item) {
            (new StockInventoryRepository($this->kondisi, $this->gudangId, $item))->rollback('stock_keluar');
        }
        return $stockKeluar->stockKeluarDetail()->delete();
    }
}
