<?php namespace App\Haramain\Service\SistemStock;

use App\Models\Stock\StockKeluar;
use App\Models\Stock\StockMasuk;
use App\Models\Stock\StockOpname;

class StockInventoryService
{
    protected object $stockInventoryRepo;
    protected object $stockOpnameRepo;

    public function __construct(
        StockInventoryRepo $stockInventoryRepo,
        StockOpnameRepo $stockOpnameRepo
    )
    {
        $this->stockInventoryRepo = $stockInventoryRepo;
        $this->stockOpnameRepo = $stockOpnameRepo;
    }

    public function handleClean()
    {
        $this->stockInventoryRepo->cleanAll();
    }

    public function handleGenerateStockOpname(): object
    {
        $dataStockOpname = $this->stockOpnameRepo->getStockOpnameDetail(session('ClosedCash'));
        \DB::beginTransaction();
        try {
            //$this->stockInventoryRepo->clean('stock_opname');
            foreach ($dataStockOpname as $item) {
                $field = 'stock_opname';
                $this->stockInventoryRepo->store($item->jenis, $item->gudang_id, $field, $item);
            }
            \DB::commit();
            return (object)['status'=>true, 'keterangan'=>'Data Sukses Tersimpan'];
        } catch (\Exception $e){
            \DB::rollBack();
            return (object)['status'=>false, 'keterangan'=>$e];
        }
    }

    public function handleGenerateStockMasuk(): object
    {
        $dataStockMasuk = StockMasuk::query()->where('active_cash', session('ClosedCash'))->get();
        \DB::beginTransaction();
        try {
            foreach ($dataStockMasuk as $item) {
                $kondisi = $item->kondisi;
                $gudang = $item->gudang_id;
                $field = 'stock_masuk';
                $stockMasukDetail = $item->stockMasukDetail;
                foreach ($stockMasukDetail as $row) {
                    $this->stockInventoryRepo->store($kondisi, $gudang, $field, $row);
                }
            }
            \DB::commit();
            return (object)['status'=>true, 'keterangan'=>'Data Sukses Tersimpan'];
        } catch (\Exception $e){
            \DB::rollBack();
            return (object)['status'=>false, 'keterangan'=>'Gagal Generate'];
        }
    }

    public function handleGenerateStockKeluar(): object
    {
        $dataStockKeluar = StockKeluar::query()->active(session('ClosedCash'))->get();
        \DB::beginTransaction();
        try {
            foreach ($dataStockKeluar as $item) {
                $kondisi = $item->kondisi;
                $gudang = $item->gudang_id;
                $field = 'stock_keluar';
                $stockKeluarDetail = $item->stockKeluarDetail;
                foreach ($stockKeluarDetail as $row) {
                    $this->stockInventoryRepo->store($kondisi, $gudang, $field, $row);
                }
            }
            \DB::commit();
            return (object)['status'=>true, 'keterangan'=>'Data Sukses Tersimpan'];
        } catch (\Exception $e){
            \DB::rollBack();
            return (object)['status'=>false, 'keterangan'=>'Gagal Generate'];
        }
    }
}
