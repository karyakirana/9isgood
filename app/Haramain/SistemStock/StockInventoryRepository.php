<?php namespace App\Haramain\SistemStock;

use App\Models\Stock\StockInventory;

class StockInventoryRepository
{
    public function update($kondisi, $gudangId, $field, $dataItem)
    {
        $stock = $this->query($kondisi, $gudangId, $dataItem);
        if ($stock->doesntExist()){
            return $this->create($kondisi, $gudangId, $field, $dataItem);
        }
        $stock->increment($field, $dataItem->jumlah);
        if ($field == 'stock_keluar'){
            return $stock->decrement('stock_saldo', $dataItem->jumlah);
        }
        return $stock->increment('stock_saldo', $dataItem->jumlah);
    }

    public function updateDecrement($kondisi, $gudangId, $field, $dataItem)
    {
        $stock = $this->query($kondisi, $gudangId, $dataItem);
        $stock->increment($field, $dataItem->jumlah);
        return $stock->decrement('stock_saldo', $dataItem->jumlah);
    }

    public function rollback($kondisi, $gudangId, $field, $dataItem)
    {
        $query = $this->query($kondisi, $gudangId, $dataItem);
        if ($query->doesntExist()){
            return null;
        }
        $query->decrement($field, $dataItem->jumlah);
        if ($field == 'stock_keluar'){
            return $query->increment('stock_saldo', $dataItem->jumlah);
        }
        return $query->decrement('stock_saldo', $dataItem->jumlah);
    }

    public function rollbackDecrement($kondisi, $gudangId, $field, $dataItem)
    {
        $query = $this->query($kondisi, $gudangId, $dataItem);
        if ($query->doesntExist()){
            return null;
        }
        $query->increment($field, $dataItem->jumlah);
        return $query->increment('stock_saldo', $dataItem->jumlah);
    }

    protected function create($kondisi, $gudangId, $field, $dataItem)
    {
        return StockInventory::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jenis'=>$kondisi,
                'gudang_id'=>$gudangId,
                'produk_id'=>$dataItem->produk_id,
                $field=>$dataItem->jumlah,
                'stock_saldo'=>($field == 'stock_keluar') ? 0 - $dataItem->jumlah : $dataItem->jumlah,
            ]);
    }

    protected function query($kondisi, $gudangId, $dataItem)
    {
        return StockInventory::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $kondisi)
            ->where('gudang_id', $gudangId)
            ->where('produk_id', $dataItem->produk_id);
    }
}
