<?php namespace App\Haramain\Service\SistemPenjualan;

use App\Haramain\Repository\Jurnal\JurnalTransaksiRepo;
use App\Haramain\Repository\Neraca\NeracaSaldoRepo;
use App\Haramain\Repository\Penjualan\PenjualanReturRepo;
use App\Haramain\Repository\Persediaan\PersediaanTransaksiRepo;
use App\Haramain\Repository\Stock\StockMasukRepo;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PenjualanReturService
{
    protected $penjualanReturRepo;
    protected $stockMasukRepo;
    protected $persediaanTransaksi;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;

    public function __construct()
    {
        $this->penjualanReturRepo = new PenjualanReturRepo();
        $this->stockMasukRepo = new StockMasukRepo();
        $this->persediaanTransaksi = new PersediaanTransaksiRepo();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldoRepo = new NeracaSaldoRepo();
    }

    public function handleGetDataById($id)
    {
        return $this->penjualanReturRepo->getData($id);
    }

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            // store penjualan retur
            $penjualanRetur = $this->penjualanReturRepo->store($data);
            // store stock masuk
            $stockMasuk = $this->stockMasukRepo->store($data, $penjualanRetur::class, $penjualanRetur->id);
            // store persediaan
            $persediaanTransaksi = $this->persediaanTransaksi->storeIn($data, $penjualanRetur::class, $penjualanRetur->id);
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
        }
    }

    public function handleUpdate($data)
    {
        \DB::beginTransaction();
        try {
            // update penjualan retur
            $penjualanRetur = $this->penjualanReturRepo->update($data);
            // update stock masuk
            $stockMasuk = $this->stockMasukRepo->update($data, $penjualanRetur::class, $penjualanRetur->id);
            // store persediaan
            $persediaanTransaksi = $this->persediaanTransaksi->updateIn($data, $penjualanRetur::class, $penjualanRetur->id);
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
        }
    }

    public function handleDestroy($id)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
        }
    }

    private function jurnalStore($penjualanReturType, $penjualanReturId):void
    {
        // jurnal piutang penjualan
        // jurnal retur penjualan
        // jurnal persediaan -- sesuai gudang
        // jurnal hpp
    }

    private function jurnalRollback($penjualanReturType, $penjualanReturId):void
    {
        // get jurnal transaksi
        $jurnalTransaksi = $this->jurnalTransaksiRepo->getData($penjualanReturType, $penjualanReturId);
        // each jurnal transaksi
        foreach ($jurnalTransaksi as $jurnalTransaksi){
            // if debet
            if ($jurnalTransaksi->debet > 0){
                //
            }
        }
    }

    private function rollback($penjualanReturType, $penjualanReturId): void
    {
        // initiate
        $penjualanRetur = $this->penjualanReturRepo->rollback($penjualanReturId);
        // rollback stock masuk
        $stockMasuk = $this->stockMasukRepo->rollback($penjualanReturType, $penjualanReturId);
        // rollback persediaan
        $persediaan = $this->stockMasukRepo->rollback($penjualanReturType, $penjualanReturId);
    }
}
