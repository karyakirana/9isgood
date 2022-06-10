<?php namespace App\Haramain\Service\SistemStock\SubStockInventory;

use App\Models\Stock\StockInventory;

class StockBehaviorRepo
{
    public function createOrUpdate(array|object $data, string $field): object
    {
        $data = (is_array($data)) ? (object) $data : $data;
        // check item by active_cash, produk_id, jenis
        $query = StockInventory::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('produk_id', $data->produk_id)
            ->where('jenis', $data->jenis);
        // if exist update
        if ($query->exists()){
            // increment fields
            $query->increment($field, $data->jumlah);
            // increment or decrement saldo
            $this->saldoUpdate($field, $data->jumlah, $query);
            return (object)['status'=>true, 'keterangan'=>'update', 'messages'=>$query];
        }
        // if false insert
        return $this->create($data, $field);
    }

    protected function create(object $data, $field): object
    {
        $saldo = ($field == 'stock_keluar') ? 0 - $data->jumlah : $data->jumlah;
        $create =  StockInventory::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'produk_id'=>$data->produk_id,
                $field=>$data->jumlah,
                'stock_saldo'=>$saldo
            ]);
        return (object)['status'=>true, 'keterangan'=>'create', 'messages'=>$create];
    }

    protected function saldoUpdate(string $field, int $jumlah, $query):void
    {
        if ($field == 'stock_keluar'){
            $query->decrement('stock_saldo', $jumlah);
        } else {
            $query->increment('stock_saldo', $jumlah);
        }
    }

    public function rollbackStock(object $data, string $field): object
    {
        $query = StockInventory::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('produk_id', $data->produk_id)
            ->where('jenis', $data->jenis);
        $query->decrement($field, $data->jumlah);
        // increment or decrement saldo
        $this->saldoRollback($field, $data->jumlah, $query);
        return (object)['status'=>true, 'keterangan'=>'rollback', 'messages'=>$query];
    }

    protected function saldoRollback(string $field, int $jumlah, $query):void
    {
        if ($field == 'stock_keluar'){
            $query->increment('stock_saldo', $jumlah);
        } else {
            $query->decrement('stock_saldo', $jumlah);
        }
    }
}
