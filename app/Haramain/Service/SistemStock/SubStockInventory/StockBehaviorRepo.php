<?php namespace App\Haramain\Service\SistemStock\SubStockInventory;

use App\Models\Stock\StockInventory;

class StockBehaviorRepo
{
    protected object $stockInventory;
    protected array|object $data;
    protected string $field;

    public function __construct(StockInventory $stockInventory)
    {
        $this->stockInventory = $stockInventory;
    }

    public function createOrUpdate(array|object $data, string $field): object
    {
        $this->data = (is_array($data)) ? (object) $data : $data;
        $this->field = $field;
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
            $this->saldoUpdate($data->jumlah, $query);
            return (object)['status'=>true, 'keterangan'=>'update', 'messages'=>$query];
        }
        // if false insert
        return $this->create();
    }

    protected function create(): object
    {
        $saldo = ($this->field == 'stock_keluar') ? 0 - $this->data->jumlah : $this->data->jumlah;
        $create =  StockInventory::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'produk_id'=>$this->data->produk_id,
                $this->field=>$this->data->jumlah,
                'stock_saldo'=>$saldo
            ]);
        return (object)['status'=>true, 'keterangan'=>'create', 'messages'=>$create];
    }

    protected function saldoUpdate(int $jumlah, $query):void
    {
        if ($this->field == 'stock_keluar'){
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
