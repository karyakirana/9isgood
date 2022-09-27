<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Haramain\ServiceInterface;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalKasRepository;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiRepo;
use App\Haramain\SistemKeuangan\SubJurnal\JurnalTransaksiServiceTrait;
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Haramain\SistemKeuangan\SubOther\KonfigurasiJurnalRepository;
use App\Models\Keuangan\PiutangInternal;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PiutangInternalService implements ServiceInterface
{
    use JurnalTransaksiServiceTrait;

    protected $akunPiutangInternal;

    public function __construct()
    {
        // load akun piutang internal
        $this->akunPiutangInternal = KonfigurasiJurnalRepository::build('piutang_internal')->getAkun();
    }

    public function handleGetData($id)
    {
        return PiutangInternalRepository::getById($id);
    }

    public function handleStore($data)
    {
        DB::beginTransaction();
        try {
            $piutangInternal = PiutangInternalRepository::store($data);
            JurnalKasRepository::storeForPiutangInternal($piutangInternal);
            $this->jurnal($piutangInternal);
            DB::commit();
            return[
                'status'=>true,
                'keterangan'=>'Data berhasil disimpan'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleUpdate($data)
    {
        DB::beginTransaction();
        try {
            $piutangInternal = PiutangInternalRepository::getById($data['piutang_internal_id']);
            $this->rollback($piutangInternal);
            $piutangInternal = PiutangInternalRepository::update($data);
            JurnalKasRepository::storeForPiutangInternal($piutangInternal);
            $this->jurnal($piutangInternal);
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
            $piutangInternal = PiutangInternalRepository::getById($id);
            $this->rollback($piutangInternal);
            $piutangInternal->delete();
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>'Data Berhasil dihapus'
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    protected function jurnal(PiutangInternal $piutangInternal)
    {
        $jurnalTransaksi = JurnalTransaksiRepo::build($piutangInternal);
        if($piutangInternal->jenis_piutang == 'penerimaan'){
            // payment debet
            foreach ($piutangInternal->paymentable as $item) {
                $jurnalTransaksi->debet($item->akun_id, $item->nominal);
                NeracaSaldoRepository::debet($item->akun_id, $item->nominal);
            }
            // piutang internal kredit
            $jurnalTransaksi->kredit($this->akunPiutangInternal, $piutangInternal->nominal);
            NeracaSaldoRepository::kredit($this->akunPiutangInternal, $piutangInternal->nominal);
        }
        if($piutangInternal->jenis_piutang == 'pengeluaran'){
            // piutang internal debet
            $jurnalTransaksi->debet($this->akunPiutangInternal, $piutangInternal->nominal);
            NeracaSaldoRepository::debet($this->akunPiutangInternal, $piutangInternal->nominal);
            // payment kredit
            foreach ($piutangInternal->paymentable as $item) {
                $jurnalTransaksi->kredit($item->akun_id, $item->nominal);
                NeracaSaldoRepository::kredit($item->akun_id, $item->nominal);
            }
        }
    }

    protected function rollback(PiutangInternal $piutangInternal)
    {
        PiutangInternalRepository::rollback($piutangInternal->id);
        $this->rollbackJurnalAndSaldo($piutangInternal);
    }
}
