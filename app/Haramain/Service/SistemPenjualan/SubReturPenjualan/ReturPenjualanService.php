<?php namespace App\Haramain\Service\SistemPenjualan\SubReturPenjualan;

use App\Haramain\Service\SistemKeuangan\Jurnal\JurnalTransaksiRepo;
use App\Haramain\Service\SistemKeuangan\Kasir\PiutangPenjualanRepo;
use App\Haramain\Service\SistemKeuangan\Kasir\PiutangPenjualanTrait;
use App\Haramain\Service\SistemKeuangan\Neraca\NeracaSaldoRepository;
use App\Haramain\Service\SistemStock\StockMasukRepo;
use App\Models\Penjualan\PenjualanRetur;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReturPenjualanService
{
    protected StockMasukRepo $stockMasukRepo;
    protected ReturPenjualanRepo $penjualanRepo;
    protected JurnalTransaksiRepo $jurnalTransaksiRepo;
    protected NeracaSaldoRepository $neracaSaldoRepository;
    protected PiutangPenjualanRepo $piutangPenjualanRepo;

    public function handleStore($data): object
    {
        \DB::beginTransaction();
        try {
            $data = (is_array($data)) ? (object) $data : $data;
            // store retur penjualan
            $penjualanRetur = $this->penjualanRepo->create($data);
            // store stock masuk
            $stockMasuk = $this->stockMasukRepo->create(PenjualanRetur::class, $penjualanRetur->id, $data);
            // store piutang_penjualan
            $this->piutangPenjualanRepo->storeRetur($penjualanRetur);
            // update saldo_piutang_penjualan
            $this->piutangPenjualanRepo->updateSaldoRetur($penjualanRetur->customer_id, $penjualanRetur->total_bayar);
            // store jurnal_persediaan
            // store debet
            $this->jurnalTransaksiRepo->createDebet($data->akun_debet, PenjualanRetur::class, $penjualanRetur->id, $penjualanRetur->total_bayar);
            // store kredit
            $this->jurnalTransaksiRepo->createKredit($data->akun_kredit, PenjualanRetur::class, $penjualanRetur->id, $penjualanRetur->total_bayar);
            // update neraca saldo debet
            $this->neracaSaldoRepository->updateDebet($data->akun_debet, $penjualanRetur->total_bayar);
            // update neraca saldo kredit
            $this->neracaSaldoRepository->updateKredit($data->akun_kredit, $penjualanRetur->total_bayar);
            \DB::commit();
            return (object)['status'=>true, 'keterangan'=>'success'];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)['status'=>true, 'keterangan'=>$e];
        }
    }

    public function handleUpdate($data): object
    {
        \DB::beginTransaction();
        try {
            $data = (is_array($data)) ? (object) $data : $data;
            // penjualan retur get data before update
            $penjualanRetur = $this->penjualanRepo->detData($data->penjualan_id);
            // rollback piutang_penjualan_retur and saldo_piutang_penjualan
            $this->piutangPenjualanRepo->rollbackPiutangRetur($penjualanRetur);
            // get jurnal transaksi
            $oldTransaksiDebet = $this->jurnalTransaksiRepo->getByDebetRow(PenjualanRetur::class, $penjualanRetur->id);
            $oldTransaksiKredit = $this->jurnalTransaksiRepo->getByKreditRow(PenjualanRetur::class, $penjualanRetur->id);
            // rollback neracaSaldoDebet
            $this->neracaSaldoRepository->rollbackDebet($oldTransaksiDebet->akun_id, $oldTransaksiDebet->nominal_debet);
            // rollback neracaSaldoKredit
            $this->neracaSaldoRepository->rollbackKredit($oldTransaksiKredit->akun_id, $oldTransaksiDebet->nominal_kredit);
            // rollback jurnak transaksi
            $this->jurnalTransaksiRepo->transaksiRollback(PenjualanRetur::class, $penjualanRetur->id);
            // update penjualan_retur
            $updatepenjualanRetur = $this->penjualanRepo->update($data->penjualanReturId, $data);
            // get stock_masuk id
            $stockMasukId = $penjualanRetur->stockMasuk->id;
            // update stock_masuk
            $this->stockMasukRepo->update($stockMasukId, $data);
            // update piutang_penjualan
            $this->piutangPenjualanRepo->updateRetur($penjualanRetur->id, $data);
            // update saldo_piutang_penjualan
            $this->piutangPenjualanRepo->updateSaldoRetur($data->customer_id, $data->total_bayar);
            // update jurnal persediaan
            // store transaksi debet
            $this->jurnalTransaksiRepo->createDebet($data->akun_debet, PenjualanRetur::class, $penjualanRetur->id, $penjualanRetur->total_bayar);
            // store transaksi kredit
            $this->jurnalTransaksiRepo->getByKreditRow($data->akun_kredit, PenjualanRetur::class, $penjualanRetur->id, $penjualanRetur->total_bayar);
            // update neraca saldo debet
            $this->neracaSaldoRepository->updateDebet($data->akun_debet, $penjualanRetur->total_bayar);
            // update neraca saldo kredit
            $this->neracaSaldoRepository->updateKredit($data->akun_kredit, $penjualanRetur->total_bayar);
            \DB::commit();
            return (object)['status'=>true, 'keterangan'=>'success'];
        }catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)['status'=>true, 'keterangan'=>$e];
        }
    }

    public function handleDestroy($data)
    {
        //
    }
}
