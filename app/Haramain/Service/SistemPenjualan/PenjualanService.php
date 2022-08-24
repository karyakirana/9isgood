<?php namespace App\Haramain\Service\SistemPenjualan;


use App\Haramain\Repository\Jurnal\JurnalTransaksiRepo;
use App\Haramain\Repository\Jurnal\PiutangPenjualanRepo;
use App\Haramain\Repository\Neraca\NeracaSaldoRepo;
use App\Haramain\Repository\Penjualan\PenjualanRepo;
use App\Haramain\Repository\Persediaan\PersediaanTransaksiRepo;
use App\Haramain\Repository\Stock\StockKeluarRepo;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PenjualanService
{
    protected $penjualanRepo;
    protected $stockKeluarRepo;
    protected $persediaanTransaksiRepo;
    protected $piutangPenjualanRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;

    public function __construct()
    {
        $this->penjualanRepo = new PenjualanRepo();
        $this->stockKeluarRepo = new StockKeluarRepo();
        $this->persediaanTransaksiRepo = new PersediaanTransaksiRepo();
        $this->piutangPenjualanRepo = new PiutangPenjualanRepo();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldoRepo = new NeracaSaldoRepo();
    }

    public function handleGetDataById($id)
    {
        return $this->penjualanRepo->getDataById($id);
    }

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            // store Penjualan
            $penjualan = $this->penjualanRepo->store($data);
            // store stock masuk
            $stockKeluar = $this->stockKeluarRepo->store($data, $penjualan::class, $penjualan->id);
            // store persediaan
            $persediaanTransaksi = $this->persediaanTransaksiRepo->storeOut($data, $penjualan::class, $penjualan->id);
            // store piutang penjualan
            $piutangPenjualan = $this->piutangPenjualanRepo->store($data, $penjualan::class, $penjualan->id);
            $this->storeJurnal($penjualan, $data, $persediaanTransaksi);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>$penjualan
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object) [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleUpdate($data)
    {
        \DB::beginTransaction();
        try {
            $penjualan = $this->penjualanRepo->getDataById($data['penjualanId']);
            // rollback
            $this->rollback($penjualan->id);
            // update penjualan
            $this->penjualanRepo->update($data);
            // update stock keluar
            $this->stockKeluarRepo->update($data, $penjualan::class, $penjualan->id);
            // update persediaan
            $persediaanTransaksi = $this->persediaanTransaksiRepo->updateOut($data, $penjualan::class, $penjualan->id);
            // update piutang penjualan
            $this->piutangPenjualanRepo->update($data, $penjualan::class, $penjualan->id);
            $this->storeJurnal($penjualan, $data, $persediaanTransaksi);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>$penjualan
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object) [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    protected function storeJurnal($penjualan, $data, $persediaanTransaksi)
    {
        // jurnal piutang
        $this->jurnalTransaksiRepo->storeDebet($penjualan::class, $penjualan->id,$data['akunPiutangId'], $data['totalBayar']);
        $this->neracaSaldoRepo->debetIncrement($data['akunPiutangId'], $data['totalBayar']);
        // jurnal ppn
        if ((int)$data['ppn'] > 0){
            $this->jurnalTransaksiRepo->storeKredit($penjualan::class, $penjualan->id, $data['akunPPNPenjualan'], $data['ppn']);
            $this->neracaSaldoRepo->kreditIncrement($data['akunPPNPenjualan'], $data['ppn']);
        }
        // jurnal biaya lain
        if ((int)$data['biayaLain'] > 0){
            $this->jurnalTransaksiRepo->storeKredit($penjualan::class, $penjualan->id, $data['akunBiayaLainPenjualan'], $data['biayaLain']);
            $this->neracaSaldoRepo->kreditIncrement($data['akunBiayaLainPenjualan'], $data['biayaLain']);
        }
        // jurnal penjualan
        $this->jurnalTransaksiRepo->storeKredit($penjualan::class, $penjualan->id, $data['akunPenjualanid'], $data['totalPenjualan']);
        $this->neracaSaldoRepo->kreditIncrement($data['akunPenjualanid'], $data['totalPenjualan']);
        // jurnal hpp
        $this->jurnalTransaksiRepo->storeDebet($penjualan::class, $penjualan->id, $data['akunHPPId'],$persediaanTransaksi['totalPersediaanKeluar']);
        $this->neracaSaldoRepo->debetIncrement($data['akunHPPId'],$persediaanTransaksi['totalPersediaanKeluar']);
        // jurnal persediaan
        $this->jurnalTransaksiRepo->storeKredit($penjualan::class, $penjualan->id, $data['akunPersediaanId'],$persediaanTransaksi['totalPersediaanKeluar']);
        $this->neracaSaldoRepo->debetDecrement($data['akunPersediaanId'],$persediaanTransaksi['totalPersediaanKeluar']);
    }

    protected function rollback($penjualanId)
    {
        // initiate penjualan
        $penjualan = $this->penjualanRepo->getDataById($penjualanId);
        $this->penjualanRepo->rollback($penjualanId);
        // rollback stock keluar
        $this->stockKeluarRepo->rollback($penjualan::class, $penjualanId);
        // rollback persediaan transaksi
        $this->persediaanTransaksiRepo->rollbackStoreOut($penjualan::class, $penjualanId);
        // rollback piutang penjualan
        $this->piutangPenjualanRepo->rollback($penjualan::class, $penjualanId);
        // rollback neraca saldo
        $jurnalTransaksi = $this->jurnalTransaksiRepo->getData($penjualan::class, $penjualanId);
        foreach ($jurnalTransaksi as $item) {
            if ($item->nominal_debet){
                $this->neracaSaldoRepo->debetDecrement($item->akun_id, $item->nominal_debet);
            }

            if ($item->nominal_kredit){
                $this->neracaSaldoRepo->kreditDecrement($item->akun_id, $item->nominal_kredit);
            }
        }
        // rollback jurnal transaksi
        $this->jurnalTransaksiRepo->rollback($penjualan::class, $penjualanId);
    }
}
