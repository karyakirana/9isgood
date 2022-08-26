<?php namespace App\Haramain\Service\Generator;

use App\Models\Keuangan\JurnalTransaksi;
use App\Models\Keuangan\NeracaSaldo;
use App\Models\Keuangan\PersediaanTransaksi;
use App\Models\Keuangan\PersediaanTransaksiDetail;
use App\Models\Purchase\Pembelian;
use App\Models\Purchase\PembelianDetail;
use App\Models\Stock\StockMasuk;
use App\Models\Stock\StockMasukDetail;

class GenPembelianInternalService
{
    protected $pembelian;
    protected $pembelianDetail;
    protected $stockMasuk;
    protected $stockMasukDetail;
    protected $persediaanTransaksi;
    protected $persediaanTransaksiDetail;
    protected $jurnalTransaksi;
    protected $neracaSaldo;

    public function __construct()
    {
        $this->pembelian = new Pembelian();
        $this->pembelianDetail = new PembelianDetail();
        $this->stockMasuk = new StockMasuk();
        $this->stockMasukDetail = new StockMasukDetail();
        $this->persediaanTransaksi = new PersediaanTransaksi();
        $this->persediaanTransaksiDetail = new PersediaanTransaksiDetail();
        $this->jurnalTransaksi = new JurnalTransaksi();
        $this->neracaSaldo = new NeracaSaldo();
    }

    public function handleGenerate()
    {
        //
    }

    /**
     * pembelian proses
     */
    private function getPembelian()
    {
        return $this->pembelian->newQuery()
            ->where('ClosedCash', session('ClosedCash'))
            ->get();
    }

    /**
     * stock masuk proses
     */
    private function storeStockMasuk($data, $stockableType, $stockableId)
    {
        //
    }

    /**
     * persediaan transaksi proses
     */
    private function storePersediaan($data, $persediaanType, $persediaanId)
    {
        //
    }
}
