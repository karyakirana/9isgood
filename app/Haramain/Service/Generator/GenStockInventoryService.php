<?php namespace App\Haramain\Service\Generator;

use App\Models\Penjualan\Penjualan;
use App\Models\Purchase\Pembelian;
use App\Models\Stock\StockInventory;
use App\Models\Stock\StockKeluar;
use App\Models\Stock\StockMasuk;
use App\Models\Stock\StockMutasi;
use App\Models\Stock\StockOpname;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GenStockInventoryService
{
    protected $stockOpname;
    protected $stockMasuk;
    protected $stockKeluar;
    protected $stockMutasi;
    protected $stockInventory;

    // revisi
    protected $pembelian;
    protected $penjualan;

    public function __construct()
    {
        $this->stockOpname = new StockOpname();
        $this->stockMasuk = new StockMasuk();
        $this->stockKeluar = new StockKeluar();
        $this->stockMutasi = new StockMutasi();
        $this->stockInventory = new StockInventory();

        // revisi
        $this->pembelian = new Pembelian();
        $this->penjualan = new Penjualan();
    }

    public function generateFromStockOpname()
    {
        \DB::beginTransaction();
        try {
            $dataStockOpname = $this->stockOpname->newQuery()->where('active_cash', session('ClosedCash'))->get();
            // rollback first
            $this->rollbackSaldoDecrementByField('stock_opname');
            // generate
            foreach ($dataStockOpname as $item) {
                // store detail
                $dataDetail = $item->stockOpnameDetail;
                foreach ($dataDetail as $value) {
                    $simpan = $this->stockSaldoIncrement($item->jenis, $item->gudang_id, $value->produk_id, 'stock_opname',$value->jumlah);
                }
            }
            \DB::commit();
            return [
                'status'=>true,
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return [
                'status'=>true,
                'keterangan'=>$e
            ];
        }
    }

    public function generateFromStockMutasi()
    {
        \DB::beginTransaction();
        try {
            $dataMutasi = $this->stockMutasi->newQuery()->where('active_cash', session('ClosedCash'))->get();
            // generate
            foreach ($dataMutasi as $item) {
                // store detail
                $dataDetail = $item->stockMutasiDetail;
                $kondisiMasuk = \Str::before($item->jenis_mutasi, '_');
                $kondisiKeluar = \Str::after($item->jenis_mutasi, '_');
                foreach ($dataDetail as $value){
                    // keluar
                    $this->stockSaldoDecrement($kondisiKeluar, $item->gudang_asal_id, $value->produk_id, 'stock_keluar', $value->jumlah);
                    // masuk
                    $this->stockSaldoIncrement($kondisiMasuk, $item->gudang_tujuan_id, $value->produk_id, 'stock_masuk', $value->jumlah);
                }
            }
            \DB::commit();
            return [
                'status'=>true,
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return [
                'status'=>true,
                'keterangan'=>$e
            ];
        }
    }

    public function generateFromPembelian()
    {
        \DB::beginTransaction();
        try {
            $dataPembelian = $this->pembelian->newQuery()->where('active_cash', session('ClosedCash'))->get();
            // generate
            foreach ($dataPembelian as $item) {
                $dataDetail = $item->pembelianDetail;
                $kondisi = 'baik';
                foreach ($dataDetail as $value) {
                    // masuk
                    $this->stockSaldoIncrement($kondisi, $item->gudang_id, $value->produk_id, 'stock_masuk', $value->jumlah);
                }
            }
            \DB::commit();
            return [
                'status'=>true,
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return [
                'status'=>true,
                'keterangan'=>$e
            ];
        }
    }

    public function generateFromPenjualan()
    {
        \DB::beginTransaction();
        try {
            $dataPenjualan = $this->penjualan->newQuery()->where('active_cash', session('ClosedCash'))->get();
            // generate
            foreach ($dataPenjualan as $item) {
                $dataDetail = $item->penjualanDetail;
                $kondisi = 'baik';
                foreach ($dataDetail as $value) {
                    // masuk
                    $this->stockSaldoDecrement($kondisi, $item->gudang_id, $value->produk_id, 'stock_keluar', $value->jumlah);
                }
            }
            \DB::commit();
            return [
                'status'=>true,
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return [
                'status'=>true,
                'keterangan'=>$e
            ];
        }
    }

    public function generateFromStockMasuk()
    {
        \DB::beginTransaction();
        try {
            $dataStockMasuk = $this->stockMasuk->newQuery()->where('active_cash', session('ClosedCash'))->get();
            // rollback first
            $this->rollbackSaldoDecrementByField('stock_masuk');
            // generate
            foreach ($dataStockMasuk as $item) {
                // store detail
                $dataDetail = $item->stockMasukDetail;
                foreach ($dataDetail as $value) {
                    $simpan = $this->stockSaldoIncrement($item->kondisi, $item->gudang_id, $value->produk_id, 'stock_masuk',$value->jumlah);
                }
            }
            \DB::commit();
            return [
                'status'=>true,
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return [
                'status'=>true,
                'keterangan'=>$e
            ];
        }
    }

    public function generateFromStockKeluar()
    {
        \DB::beginTransaction();
        try {
            $dataStockMasuk = $this->stockKeluar->newQuery()->where('active_cash', session('ClosedCash'))->get();
            // rollback first
            $this->rollbackSaldoIncrementByField('stock_keluar');
            // generate
            foreach ($dataStockMasuk as $item) {
                // store detail
                $dataDetail = $item->stockKeluarDetail;
                foreach ($dataDetail as $value) {
                    $simpan = $this->stockSaldoDecrement($item->kondisi, $item->gudang_id, $value->produk_id, 'stock_keluar',$value->jumlah);
                }
            }
            \DB::commit();
            return [
                'status'=>true,
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return [
                'status'=>true,
                'keterangan'=>$e
            ];
        }
    }

    /**
     * stock inventory proses
     */
    private function queryStockInventory($kondisi, $gudangId, $produkId)
    {
        return $this->stockInventory->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where('jenis', $kondisi)
            ->where('gudang_id', $gudangId)
            ->where('produk_id', $produkId);
    }

    private function rollbackSaldoDecrementByField($field)
    {
        $query = $this->stockInventory->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where($field, '>', 0);
        $getData = $query->get();
        foreach ($getData as $item) {
            $queryItem = $this->stockInventory->newQuery()->find($item->id);
            $queryItem->update([
                $field=>0,
                'stock_saldo'=>$queryItem->stock_saldo - $queryItem->{$field}
            ]);
        }
    }

    private function rollbackSaldoIncrementByField($field)
    {
        $query = $this->stockInventory->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where($field, '>', 0);
        $getData = $query->get();
        foreach ($getData as $item) {
            $queryItem = $this->stockInventory->newQuery()->find($item->id);
            $queryItem->update([
                $field=>0,
                'stock_saldo'=>$queryItem->stock_saldo + $queryItem->{$field}
            ]);
        }
    }

    private function stockSaldoIncrement($kondisi, $gudangId, $produkId, $field, $jumlah)
    {
        $query = $this->queryStockInventory($kondisi, $gudangId, $produkId);
        if ($query->exists()){
            $query->increment($field, $jumlah);
            return $query->increment('stock_saldo', $jumlah);
        }
        return $this->stockInventory->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jenis'=>$kondisi,
                'gudang_id'=>$gudangId,
                'produk_id'=>$produkId,
                $field=>$jumlah,
                'stock_saldo'=>$jumlah,
            ]);
    }

    private function stockSaldoDecrement($kondisi, $gudangId, $produkId, $field, $jumlah)
    {
        $query = $this->queryStockInventory($kondisi, $gudangId, $produkId);
        if ($query->exists()){
            $query->increment($field, $jumlah);
            return $query->decrement('stock_saldo', $jumlah);
        }
        return $this->stockInventory->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jenis'=>$kondisi,
                'gudang_id'=>$gudangId,
                'produk_id'=>$produkId,
                $field=>$jumlah,
                'stock_saldo'=>0 - $jumlah,
            ]);
    }
}
