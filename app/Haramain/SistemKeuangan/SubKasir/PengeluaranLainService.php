<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Haramain\ServiceInterface;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalKasRepository;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiRepo;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiServiceTrait;
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Models\Keuangan\PengeluaranLain;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PengeluaranLainService implements ServiceInterface
{
    use JurnalTransaksiServiceTrait;
    public function __construct()
    {
    }

    public function handleGetData($id)
    {
        return PengeluaranLainRepository::getDataById($id);
    }

    public function handleStore($data)
    {
        DB::beginTransaction();
        try {
            $pengeluaranLain = PengeluaranLainRepository::store($data);
            JurnalKasRepository::storeForPengeluaranLain($pengeluaranLain);
            $this->jurnal($pengeluaranLain);
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'Data berhasil disimpan'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleUpdate($data)
    {
        DB::beginTransaction();
        try {
            $pengeluaranLain = $this->handleGetData($data['pengeluaran_lain_id']);
            $this->rollback($pengeluaranLain);
            $pengeluaranLain = PengeluaranLainRepository::update($data);
            JurnalKasRepository::storeForPengeluaranLain($pengeluaranLain);
            $this->jurnal($pengeluaranLain);
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'Data berhasil diupdate'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    public function handleDestroy($id)
    {
        DB::beginTransaction();
        try {
            $pengeluaranLain = $this->handleGetData($id);
            $this->rollback($pengeluaranLain);
            $pengeluaranLain->delete();
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'Data berhasil dihapus'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    protected function jurnal(PengeluaranLain $pengeluaranLain)
    {
        $jurnalTransaksi = JurnalTransaksiRepo::build($pengeluaranLain);

        foreach ($pengeluaranLain->pengeluaranLainDetail as $pengeluaranLainDetail){
            // debet
            $jurnalTransaksi->debet($pengeluaranLainDetail->akun_id, $pengeluaranLainDetail->nominal);
            NeracaSaldoRepository::kredit($pengeluaranLainDetail->akun_id, $pengeluaranLainDetail->nominal);
        }
        foreach ($pengeluaranLain->paymentable as $payment){
            // kredit
            $jurnalTransaksi->kredit($payment->akun_id, $payment->nominal);
            NeracaSaldoRepository::kredit($payment->akun_id, $payment->nominal);
        }
    }

    protected function rollback(PengeluaranLain $pengeluaranLain)
    {
        JurnalKasRepository::rollbackForPengeluaranLain($pengeluaranLain);
        PengeluaranLainRepository::rollback($pengeluaranLain->id);
        $this->rollbackJurnalAndSaldo($pengeluaranLain);
    }
}
