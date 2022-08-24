<?php namespace App\Haramain\Service\SistemStock;

use App\Haramain\Repository\Jurnal\JurnalTransaksiRepo;
use App\Haramain\Repository\Neraca\NeracaSaldoRepo;
use App\Haramain\Repository\Persediaan\PersediaanMutasiRepo;
use App\Haramain\Repository\Persediaan\PersediaanTransaksiRepo;
use App\Haramain\Repository\Stock\StockMutasiRepo;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StockMutasiService
{
    protected $stockMutasiRepo;
    protected $stockMasukRepo;
    protected $persediaanMutasiRepo;
    protected $persediaanTransaksiRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;

    public function __construct()
    {
        $this->stockMutasiRepo = new StockMutasiRepo();
        $this->stockMasukRepo = new StockMutasiRepo();
        $this->persediaanMutasiRepo = new PersediaanMutasiRepo();
        $this->persediaanTransaksiRepo = new PersediaanTransaksiRepo();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldoRepo = new NeracaSaldoRepo();
    }

    public function getData($id)
    {
        //
    }

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
            return (object)[
                'status'=>true
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>true
            ];
        }
    }
}
