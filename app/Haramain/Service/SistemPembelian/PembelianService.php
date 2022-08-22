<?php namespace App\Haramain\Service\SistemPembelian;

use App\Haramain\Repository\Jurnal\HutangPembelianRepo;
use App\Haramain\Repository\Jurnal\JurnalTransaksiRepo;
use App\Haramain\Repository\Neraca\NeracaSaldoRepo;
use App\Haramain\Repository\Pembelian\PembelianRepository;
use App\Haramain\Repository\Persediaan\PersediaanTransaksiRepo;
use App\Haramain\Repository\Stock\StockMasukRepo;

class PembelianService
{
    protected $pembelianRepository;
    protected $stockMasukRepository;
    protected $persediaanTransaksiRepo;
    protected $saldoHutangPembelianRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;

    public function __construct()
    {
        // initiate
        $this->pembelianRepository = new PembelianRepository();
        $this->stockMasukRepository = new StockMasukRepo();
        $this->persediaanTransaksiRepo = new PersediaanTransaksiRepo();
        $this->saldoHutangPembelianRepo = new HutangPembelianRepo();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldoRepo = new NeracaSaldoRepo();
    }

    public function getDataById($pembelianId)
    {
        return $this->pembelianRepository->getData($pembelianId);
    }

    public function store($data)
    {
        // store pembelian
        $pembelian = $this->pembelianRepository->store($data);
        // store stock masuk
        $stockMasuk = $this->stockMasukRepository->store($data, $pembelian::class, $pembelian->id);
        // store persediaan transaksi
        $persediaanTransaksi = $this->persediaanTransaksiRepo->storeIn($data, $pembelian::class, $pembelian->id);
        // saldo hutang pembelian store
        $saldoHutangPembelian = $this->saldoHutangPembelianRepo->store($data, $pembelian::class, $pembelian->id);
        $this->jurnalStore($pembelian::class, $pembelian->id, $data);
        return $pembelian;
    }

    public function update($data)
    {
        // initiate pembelian
        $pembelian = $this->pembelianRepository->getData($data['pembelianId']);
        // rollback
        $this->rollback($pembelian->id);
        // update pembelian
        $this->pembelianRepository->update($data);
        // update persediaan
        $this->persediaanTransaksiRepo->updateIn($data, $pembelian::class, $pembelian->id);
        // saldo hutang pembelian
        $this->saldoHutangPembelianRepo->update($data, $pembelian::class, $pembelian->id);
        // update stock masuk
        $stockMasuk = $this->stockMasukRepository->update($data, $pembelian::class, $pembelian->id);
        // update persediaan transaksi
        $persediaanTransaksi = $this->persediaanTransaksiRepo->updateIn($data, $pembelian::class, $pembelian->id);
        $this->jurnalStore($pembelian::class, $pembelian->id, $data);
        return $pembelian;
    }

    public function rollback($pembelianId)
    {
        // initiate pembelian
        $pembelian = $this->pembelianRepository->getData($pembelianId);
        // rollback stock masuk
        $stockMasuk = $this->stockMasukRepository->rollback($pembelian::class, $pembelian->id);
        // rollback persediaan transaksi
        $persediaanTransaksi = $this->persediaanTransaksiRepo->rollbackStoreIn($pembelian::class, $pembelian->id);
        // rollback hutang pembelian
        $hutangPembelian = $this->saldoHutangPembelianRepo->rollback($pembelian::class, $pembelian->id);
        // rollback pembelian
        $rollbackPembelian = $this->pembelianRepository->rollback($pembelianId);
        // get data neraca saldo
        $jurnalTransaksi = $this->jurnalTransaksiRepo->getData($pembelian::class, $pembelian->id);
        // rollback neraca saldo
        foreach ($jurnalTransaksi as $item) {
            if ($item->nominal_debet){
                $this->neracaSaldoRepo->debetDecrement($item->akun_id, $item->nominal_debet);
            }

            if ($item->nominal_kredit){
                $this->neracaSaldoRepo->kreditDecrement($item->akun_id, $item->nominal_kredit);
            }
        }
        // rollback jurnal transaksi
        $this->jurnalTransaksiRepo->rollback($pembelian::class, $pembelian->id);

    }

    protected function jurnalStore($pembelianableType, $pembelianableId, $data)
    {
        // jurnal transaksi persediaan
        $this->jurnalTransaksiRepo->storeDebet($pembelianableType, $pembelianableId, $data['akunPersediaanId'], $data['totalPembelian']);
        $this->neracaSaldoRepo->debetIncrement($data['akunPersediaanId'], $data['totalPembelian']);
        // jurnal transaksi ppn pembelian
        if ((int)$data['ppn'] > 0 ){
            $this->jurnalTransaksiRepo->storeDebet($pembelianableType, $pembelianableId, $data['akunPPNPembelianId'], $data['ppn']);
            $this->neracaSaldoRepo->debetIncrement($data['akunPPNPembelianId'], $data['ppn']);
        }
        // jurnal transaksi biaya lain pembelian
        if ((int)$data['biayaLain'] > 0 ){
            $this->jurnalTransaksiRepo->storeDebet($pembelianableType, $pembelianableId, $data['akunBiayaLainPembelianId'], $data['biayaLain']);
            $this->neracaSaldoRepo->debetIncrement($data['akunBiayaLainPembelianId'], $data['biayaLain']);
        }
        // jurnal transaksi hutang dagang
        $this->jurnalTransaksiRepo->storeKredit($pembelianableType, $pembelianableId, $data['akunHutangPembelianId'], $data['totalBayar']);
        $dd = $this->neracaSaldoRepo->kreditIncrement($data['akunHutangPembelianId'], $data['totalBayar']);
        //dd($dd);
    }
}
