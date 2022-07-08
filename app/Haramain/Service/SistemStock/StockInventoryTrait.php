<?php namespace App\Haramain\Service\SistemStock;

use App\Models\Stock\StockInventory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait StockInventoryTrait
{
    private function stockIncrement($jenis, $gudang_id, $produk_id, $field, $jumlah): Model|Builder|int
    {
        $builder = StockInventory::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $jenis)
            ->where('gudang_id', $gudang_id)
            ->where('produk_id', $produk_id);
        if ($builder->doesntExist()){
            return $builder->create([
                'active_cash'=>session('ClosedCash'),
                'jenis'=>$jenis,
                'gudang_id'=>$gudang_id,
                'produk_id'=>$produk_id,
                $field=>$jumlah,
                'stock_saldo'=>($field == 'stock_keluar') ? 0 - $jumlah : $jumlah
            ]);
        }
        $builder->increment($field, $jumlah);
        if ($field == 'stock_keluar'){
            return $builder->decrement('stock_saldo', $jumlah);
        }
        return $builder->increment('stock_saldo', $jumlah);
    }

    private function stockRollback($jenis, $gudang_id, $produk_id, $field, $jumlah): int
    {
        $builder = StockInventory::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $jenis)
            ->where('gudang_id', $gudang_id)
            ->where('produk_id', $produk_id);

        $builder->decrement($field, $jumlah);
        if ($field == 'stock_keluar'){
            return $builder->increment('stock_saldo', $jumlah);
        }
        return $builder->decrement('stock_saldo', $jumlah);
    }
}
