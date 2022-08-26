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

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            \DB::commit();
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
        }
    }
}
