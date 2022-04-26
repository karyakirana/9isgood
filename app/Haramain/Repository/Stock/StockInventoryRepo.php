<?php namespace App\Haramain\Repository\Stock;

use App\Models\Stock\StockInventory;

class StockInventoryRepo
{
    protected $closedCash;
    protected $query;

    public function __construct()
    {
        $this->closedCash = session('ClosedCash');
    }

    public function check($produk_id, $gudang, $kondisi, $jumlah)
    {
        $query = StockInventory::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('produk_id', $produk_id)
            ->where('jenis', $kondisi)
            ->where('gudang_id', $gudang);

        if ($query->doesntExist()){
            return (object) ['status'=>false, 'keterangan'=>'barang tidak ada'];
        }

        if ($query->sum('stock_saldo') < $jumlah){
            return (object) ['status'=>false, 'keterangan'=>'jumlah barang tidak mencukupi '.$query->sum('stock_saldo')];
        }

        return (object) ['status'=>true, 'keterangan'=>'jumlah barang '.$query->sum('stock_saldo')];
    }

    public function incrementArrayData(array $data, $gudang, $kondisi, $field)
    {
        // initiate query
        $query = StockInventory::query()
            ->where('active_cash', $this->closedCash)
            ->where('produk_id', $data['produk_id'])
            ->where('gudang_id', $gudang)
            ->where('jenis', $kondisi);

        // jika data tidak ditemukan
        if ($query->doesntExist()){
            return StockInventory::query()->create([
                'active_cash'=>$this->closedCash,
                'jenis'=>$kondisi,
                'gudang_id'=>$gudang,
                'produk_id'=>$data['produk_id'],
                $field=>$data['jumlah'],
                // jika stock keluar maka mengurangi saldo, tidak peduli data tersebut ada atau tidak
                'stock_saldo'=>($field=='stock_keluar') ? 0 - $data['jumlah'] : $data['jumlah'],
            ]);
        }

        // data ada
        $query->increment($field, $data['jumlah']);

        if ($field=='stock_keluar'){
            return $query->decrement('stock_saldo', $data['jumlah']);
        }

        return $query->increment('stock_saldo', $data['jumlah']);
    }

    public function incremetObjectData(object $data, $gudang, $kondisi, $field, $closedCash = null)
    {
        // initiate query
        $query = StockInventory::query()
            ->where('active_cash', $closedCash ?? $this->closedCash)
            ->where('produk_id', $data->produk_id)
            ->where('gudang_id', $gudang)
            ->where('jenis', $kondisi);

        // jika data tidak ditemukan
        if ($query->doesntExist()){
            return StockInventory::query()->create([
                'active_cash'=>$closedCash ?? $this->closedCash,
                'jenis'=>$kondisi,
                'gudang_id'=>$gudang,
                'produk_id'=>$data->produk_id,
                $field=>$data->jumlah,
                // jika stock keluar maka mengurangi saldo, tidak peduli data tersebut ada atau tidak
                'stock_saldo'=>($field=='stock_keluar') ? 0 - $data->jumlah : $data->jumlah,
            ]);
        }

        // data ada
        $query->increment($field, $data->jumlah);

        if ($field=='stock_keluar'){
            return $query->decrement('stock_saldo', $data->jumlah);
        }

        return $query->increment('stock_saldo', $data->jumlah);
    }

    public function rollback(object $data, $gudang, $kondisi, $field)
    {
        // initiate query
        $query = StockInventory::query()
            ->where('active_cash', $this->closedCash)
            ->where('produk_id', $data->produk_id)
            ->where('gudang_id', $gudang)
            ->where('jenis', $kondisi);

        // data berkurang sesuai keadaan belum ada ini
        $query->decrement($field, $data->jumlah);

        // rollback jika stock_keluar saldo tentu bertambah sebelum ini
        if ($field=='stock_keluar'){
            return $query->increment('stock_saldo', $data->jumlah);
        }

        return $query->decrement('stock_saldo', $data->jumlah);
    }
}
