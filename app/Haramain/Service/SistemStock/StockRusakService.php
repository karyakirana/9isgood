<?php namespace App\Haramain\Service\SistemStock;

use App\Haramain\Repository\Jurnal\JurnalTransaksiRepo;
use App\Haramain\Repository\Neraca\NeracaSaldoRepo;
use App\Haramain\Repository\Persediaan\PersediaanMutasiRepo;
use App\Haramain\Repository\Persediaan\PersediaanTransaksiRepo;
use App\Haramain\Repository\Stock\StockKeluarRepo;
use App\Haramain\Repository\Stock\StockMasukRepo;
use App\Haramain\Repository\Stock\StockMutasiRepo;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StockRusakService
{
    protected $stockMutasiRepo;
    protected $stockKeluarRepo;
    protected $stockMasukRepo;
    protected $persediaanMutasiRepo;
    protected $persediaanTransaksiRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;

    public function __construct()
    {
        $this->stockMutasiRepo = new StockMutasiRepo();
        $this->stockKeluarRepo = new StockKeluarRepo();
        $this->stockMasukRepo = new StockMasukRepo();
        $this->persediaanMutasiRepo = new PersediaanMutasiRepo();
        $this->persediaanTransaksiRepo = new PersediaanTransaksiRepo();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldoRepo = new NeracaSaldoRepo();
    }

    public function handleGetData($id)
    {
        return $this->stockMutasiRepo->getDataById($id);
    }

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            $stockMutasi = $this->stockMutasiRepo->store($data);
            $persediaanMutasi = $this->persediaanMutasiRepo->store($stockMutasi->id, $data);
            // stock keluar baik
            $stockKeluar = $this->stockKeluarRepo->store($data, $stockMutasi::class, $stockMutasi->id);
            $persediaanTransaksiKeluar = $this->persediaanTransaksiRepo->storeOut($data, $persediaanMutasi::class, $persediaanMutasi->id);
            // stock masuk rusak
            $stockMasuk = $this->stockMasukRepo->store($data, $stockMutasi::class, $stockMutasi->id);
            // get data detail persediaan_transaksi_detail keluar
            $dataDetailStockMutasiOut = $this->persediaanTransaksiRepo->getPersediaanDetailArray($persediaanTransaksiKeluar['persediaanTransaksi']->id);
            $persediaanTransaksiMasuk = $this->persediaanTransaksiRepo->storeMutasiIn($data, $dataDetailStockMutasiOut, $persediaanMutasi::class, $persediaanMutasi->id);
            $this->storeJurnal($data, $persediaanMutasi::class, $persediaanMutasi->id, $persediaanTransaksiKeluar['totalPersediaanKeluar']);
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
        }
    }

    protected function storeJurnal($data, $mutasiType, $mutasiId, $nominal)
    {
        // persediaan rusak debet, persediaan baik kredit
        $this->jurnalTransaksiRepo->storeDebet($mutasiType, $mutasiId, $data['akunPersediaanRusakId'], $nominal);
        $this->neracaSaldoRepo->debetIncrement($data['akunPersediaanRusakId'], $nominal);
        $this->jurnalTransaksiRepo->storeKredit($mutasiType, $mutasiId, $data['akunPersediaanId'], $nominal);
        $this->neracaSaldoRepo->debetDecrement($data['akunPersediaanId'], $nominal);
    }

    public function handleUpdate($data)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
        }
    }

    protected function handleRollback($id)
    {
        //
    }

    public function handleDelete($id)
    {
        //
    }
}
