<?php namespace App\Haramain\SistemStock;

use App\Models\Stock\StockInventory;

class StockInventoryStaticRepo
{
    protected static function query($kondisi, $gudang_id, $produk_id)
    {
        return StockInventory::where('active_cash', session('ClosedCash'))
            ->where('jenis', $kondisi)
            ->where('gudang_id', $gudang_id)
            ->where('produk_id', $produk_id);
    }

    public static function stockOpnameChange($type, $kondisi, $gudang_id, $produk_id, $jumlah)
    {
        $type = ($type == 'tambah') ? 'increment' : 'decrement';
        $query = self::query($kondisi, $gudang_id, $produk_id);
        $query->{$type}('stock_opname', $jumlah);
        return $query->{$type}('stock_saldo', $jumlah);
    }

    public static function stockOpnameChangeRollback($type, $kondisi, $gudang_id, $produk_id, $jumlah)
    {
        $type = ($type == 'tambah') ? 'decrement' : 'increment';
        $query = self::query($kondisi, $gudang_id, $produk_id);
        $query->{$type}('stock_opname', $jumlah);
        return $query->{$type}('stock_saldo', $jumlah);
    }
}
