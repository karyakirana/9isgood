<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Haramain\ServiceInterface;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalKasRepository;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiRepo;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiServiceTrait;
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Models\Keuangan\PenerimaanLain;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PenerimaanLainService implements ServiceInterface
{
    use JurnalTransaksiServiceTrait;
    public function __construct()
    {
    }

    public function handleGetData($id)
    {
        return PenerimaanLainRepository::getById($id);
    }

    public function handleStore($data)
    {
        DB::beginTransaction();
        try {
            $penerimaanLain = PenerimaanLainRepository::store($data);
            JurnalKasRepository::storeForPenerimaanLain($penerimaanLain);
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'data berhasil disimpan'
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
            $penerimaanLain = PenerimaanLainRepository::update($data);
            JurnalKasRepository::storeForPenerimaanLain($penerimaanLain);
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'data berhasil diupdate'
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
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'data berhasil dihapus'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    protected function jurnal(PenerimaanLain $penerimaanLain)
    {
        $jurnalTransaksi = JurnalTransaksiRepo::build($penerimaanLain);

        foreach ($penerimaanLain->paymentable as $payment)
        {
            // debet
            $jurnalTransaksi->debet($payment->akun_id, $payment->nominal);
            NeracaSaldoRepository::debet($payment->akun_id, $payment->nominal);
        }
        foreach ($penerimaanLain->penerimaanLainDetail as $penerimaanLainDetail){
            // kredit
            $jurnalTransaksi->kredit($penerimaanLainDetail->akun_id, $penerimaanLainDetail->nominal);
            NeracaSaldoRepository::kredit($penerimaanLainDetail->akun_id, $penerimaanLainDetail->nominal);
        }
    }

    protected function rollback(PenerimaanLain $penerimaanLain)
    {
        JurnalKasRepository::rollbackForPenerimaanLain($penerimaanLain);
        PenerimaanLainRepository::rollback($penerimaanLain->id);
        $this->rollbackJurnalAndSaldo($penerimaanLain);
    }
}
