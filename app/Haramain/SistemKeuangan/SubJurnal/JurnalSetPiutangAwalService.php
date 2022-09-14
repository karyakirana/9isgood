<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Haramain\ServiceInterface;
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Models\KonfigurasiJurnal;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * store set piutang awal
 * update set piutang awal
 *
 * jurnal :
 * piutang debet
 * modal kredit
 */
class JurnalSetPiutangAwalService implements ServiceInterface
{
    use JurnalTransaksiServiceTrait;

    protected $jurnalSetPiutangAwalRepo;
    protected $jurnalTransaksiRepo;
    protected $neracaSaldoRepo;

    protected $akunPiutangAwal;
    protected $akunModalAwal;

    public function __construct()
    {
        $this->jurnalSetPiutangAwalRepo = new JurnalSetPiutangAwalRepo();
        $this->jurnalTransaksiRepo = new JurnalTransaksiRepo();
        $this->neracaSaldoRepo = new NeracaSaldoRepository();

        $this->akunPiutangAwal = KonfigurasiJurnal::query()->firstWhere('config', 'piutang_usaha')->akun_id;
        $this->akunModalAwal = KonfigurasiJurnal::query()->firstWhere('config', 'modal_piutang_awal')->akun_id;
    }

    public function handleGetData($id)
    {
        return $this->jurnalSetPiutangAwalRepo->getDataById($id);
    }

    public function handleStore($data)
    {
        \DB::beginTransaction();
        try {
            // store
            $jurnalSetPiutangAwal = $this->jurnalSetPiutangAwalRepo->store($data);
            $this->jurnal($jurnalSetPiutangAwal);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Set Piutang Awal sudah Disimpan'
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleUpdate($data)
    {
        \DB::beginTransaction();
        try {
            // rollback
            $this->jurnalSetPiutangAwalRepo->rollback($data->jurnalSetPiutangAwalId);
            // update
            $jurnalSetPiutangAwal = $this->jurnalSetPiutangAwalRepo->update($data);
            // rollback jurnal
            $this->rollbackJurnalAndSaldo($jurnalSetPiutangAwal);
            // store jurnal
            $this->jurnal($jurnalSetPiutangAwal);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Set Piutang Awal sudah Disimpan'
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    public function handleDestroy($id)
    {
        \DB::beginTransaction();
        try {
            $jurnalPiutangSetAwal = $this->jurnalSetPiutangAwalRepo->getDataById($id);
            $this->rollbackJurnalAndSaldo($jurnalPiutangSetAwal);
            $this->jurnalSetPiutangAwalRepo->destroy($jurnalPiutangSetAwal->id);
            \DB::commit();
            return (object)[
                'status'=>true,
                'keterangan'=>'Set Piutang Awal sudah Disimpan'
            ];
        } catch (ModelNotFoundException $e){
            \DB::rollBack();
            return (object)[
                'status'=>false,
                'keterangan'=>$e
            ];
        }
    }

    protected function jurnal($jurnalSetPiutangAwal)
    {
        // debet
        $this->jurnalTransaksiRepo->debet($jurnalSetPiutangAwal::class, $jurnalSetPiutangAwal->id, $this->akunPiutangAwal, $jurnalSetPiutangAwal->total_piutang);
        $this->jurnalTransaksiRepo->kredit($jurnalSetPiutangAwal::class, $jurnalSetPiutangAwal->id, $this->akunModalAwal, $jurnalSetPiutangAwal->total_piutang);
    }
}
