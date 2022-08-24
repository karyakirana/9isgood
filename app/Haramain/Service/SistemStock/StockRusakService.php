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
        return;
    }

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            $stockMutasi = $this->stockMutasiRepo->store($data);
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
        }
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
