<?php namespace App\Haramain\Service\SistemStock;

use App\Models\Stock\StockInventory;

class StockRepository
{
    protected $stockInventory;

    public function __construct()
    {
        $this->stockInventory = new StockInventory();
    }

    protected function newStockIn($kondisi, $gudangId, $produkId, $field, $jumlah)
    {
        return $this->stockInventory->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jenis'=>$kondisi,
                'gudang_id'=>$gudangId,
                'produk_id'=>$produkId,
                $field=>$jumlah,
                'stock_saldo'=>$jumlah,
                'stock_akhir',
                'stock_lost',
            ]);
    }

    protected function newStockOut($kondisi, $gudangId, $produkId, $field, $jumlah)
    {
        return $this->stockInventory->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jenis'=>$kondisi,
                'gudang_id'=>$gudangId,
                'produk_id'=>$produkId,
                $field=>$jumlah,
                'stock_saldo'=>0 - (int) $jumlah,
                'stock_akhir',
                'stock_lost',
            ]);
    }

    protected function query($kondisi, $gudangId, $produkId)
    {
        return $this->stockInventory->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $kondisi)
            ->where('gudang_id', $gudangId)
            ->where('produk_id', $produkId);
    }

    public function stockInIncrement($kondisi, $gudangId, $produkId, $field, $jumlah)
    {
        $query = $this->query($kondisi, $gudangId, $produkId);
        if ($query->exists()){
            // increment
            $query->increment($field, $jumlah);
            return $query->increment($field, $jumlah);
        }
        return $this->newStockIn($kondisi, $gudangId, $produkId, $field, $jumlah);
    }

    public function stockOutIncrement($kondisi, $gudangId, $produkId, $field, $jumlah)
    {
        $query = $this->query($kondisi, $gudangId, $produkId);
        if ($query->exists()){
            // increment
            $query->increment($field, $jumlah);
            return $query->increment($field, 0 - (int) $jumlah);
        }
        return $this->newStockOut($kondisi, $gudangId, $produkId, $field, $jumlah);
    }

    public function rollbackStockIn($kondisi, $gudangId, $produkId, $field, $jumlah)
    {
        $query = $this->query($kondisi, $gudangId, $produkId);
        $query->decrement($field, $jumlah);
        return $query->decrement($field, $jumlah);
    }

    public function rollbackStockOut($kondisi, $gudangId, $produkId, $field, $jumlah)
    {
        $query = $this->query($kondisi, $gudangId, $produkId);
        $query->decrement($field, $jumlah);
        return $query->increment($field, $jumlah);
    }
}
