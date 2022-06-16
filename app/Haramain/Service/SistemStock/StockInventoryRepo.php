<?php namespace App\Haramain\Service\SistemStock;

use App\Models\Stock\StockInventory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StockInventoryRepo
{
    public function store(string $kondisi,int $gudang,string $field,object|array $data): Model|Builder|int
    {
        $data = (is_array($data)) ? (object)$data : $data;
        $saldo = ($field == 'stock_keluar') ? 0 - $data->jumlah : $data->jumlah;
        $query = StockInventory::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $kondisi)
            ->where('gudang_id', $gudang)
            ->where('produk_id', $data->produk_id);
        if ($query->doesntExist()){
            return $query->create([
                'active_cash'=>session('ClosedCash'),
                'jenis'=>$kondisi,
                'gudang_id'=>$gudang,
                'produk_id'=>$data->produk_id,
                $field=>$data->jumlah,
                'stock_saldo'=>$saldo,
            ]);
        }
        $query->increment($field, $data->jumlah);
        return $query->increment('stock_saldo', $saldo);
    }

    public function clean(string $field): void
    {
        $query = StockInventory::query()
            ->where('active_cash', session('ClosedCash'))
            ->get();
        foreach ($query as $item) {
            $q = StockInventory::query()->find($item->id);
            if ($field=='stock_keluar'){
                $q->increment('stock_saldo', $q->{$field});
            } else {
                $q->decrement('stock_saldo', $q->{$field});
            }
            $q->update([$field=>0]);
        }
    }

    public function cleanAll():void
    {
        StockInventory::query()
            ->where('active_cash', session('ClosedCash'))
            ->update([
                'stock_awal'=>0,
                'stock_opname'=>0,
                'stock_masuk'=>0,
                'stock_keluar'=>0,
                'stock_saldo'=>0,
                'stock_akhir'=>0,
                'stock_lost'=>0,
            ]);
    }
}
