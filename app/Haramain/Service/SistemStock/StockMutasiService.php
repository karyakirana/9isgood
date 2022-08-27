<?php namespace App\Haramain\Service\SistemStock;

use App\Haramain\Repository\Jurnal\JurnalTransaksiRepo;
use App\Haramain\Repository\Neraca\NeracaSaldoRepo;
use App\Haramain\Repository\Persediaan\PersediaanMutasiRepo;
use App\Haramain\Repository\Persediaan\PersediaanTransaksiMutasiRepo;
use App\Haramain\Repository\Stock\StockKeluarRepo;
use App\Haramain\Repository\Stock\StockMasukRepo;
use App\Haramain\Repository\Stock\StockMutasiRepo;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StockMutasiService
{
    protected $stockMutasiRepo;
    protected $stockMasukRepo;
    protected $stockKeluarRepo;
    protected $persediaanMutasiRepo;
    protected $persediaanTransaksiRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;

    public function __construct()
    {
        $this->stockMutasiRepo = new StockMutasiRepo();
        $this->stockMasukRepo = new StockMasukRepo();
        $this->stockKeluarRepo = new StockKeluarRepo();
        $this->persediaanMutasiRepo = new PersediaanMutasiRepo();
        $this->persediaanTransaksiRepo = new PersediaanTransaksiMutasiRepo();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldoRepo = new NeracaSaldoRepo();
    }

    public function getData($id)
    {
        return $this->stockMutasiRepo->getDataById($id);
    }

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            // simpan dan initiate stock mutasi
            $stockMutasi = $this->stockMutasiRepo->store($data);
            // get data from persediaan
            $dataPersediaanOut = $this->persediaanTransaksiRepo->getStockOut($data);
            //dd($dataPersediaanOut);
            // store persediaan mutasi return array object persediaan mutasi dan totalPersediaanKeluar
            $persediaanMutasi = $this->persediaanMutasiRepo->store($stockMutasi->id, $data, $dataPersediaanOut);
            // stock keluar baik
            $stockKeluar = $this->stockKeluarRepo->store($data, $stockMutasi::class, $stockMutasi->id);
            $persediaanTransaksiKeluar = $this->persediaanTransaksiRepo->storeMutasiOut($data, $dataPersediaanOut, $persediaanMutasi::class, $persediaanMutasi->id);
            // stock masuk rusak
            $stockMasuk = $this->stockMasukRepo->store($data, $stockMutasi::class, $stockMutasi->id);
            // get data detail persediaan_transaksi_detail keluar
            $persediaanTransaksiMasuk = $this->persediaanTransaksiRepo->storeMutasiIn($data, $dataPersediaanOut, $persediaanMutasi::class, $persediaanMutasi->id);
            \DB::commit();
            return (object)[
                'status'=>true
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>false
            ];
        }
    }

    public function handleUpdate($data)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
            return (object)[
                'status'=>true
            ];
        } catch(ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>false
            ];
        }
    }
}
