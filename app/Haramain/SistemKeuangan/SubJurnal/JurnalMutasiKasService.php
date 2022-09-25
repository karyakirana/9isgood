<?php namespace App\Haramain\SistemKeuangan\SubJurnal;


use App\Haramain\ServiceInterface;
use App\Haramain\SistemKeuangan\SubNeraca\NeracaSaldoRepository;
use App\Models\Keuangan\KasMutasi;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class JurnalMutasiKasService implements ServiceInterface
{
    use JurnalTransaksiServiceTrait;

    public function __construct()
    {
    }

    public function handleGetData($id)
    {
        return JurnalKasMutasiRepo::getById($id);
    }

    public function handleStore($data)
    {
        DB::beginTransaction();
        try {
            $jurnalMutasi = JurnalKasMutasiRepo::store($data);
            $this->jurnal($jurnalMutasi);
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>"Jurnal dengan Kode $jurnalMutasi->kode berhasil disimpan"
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
            $jurnalMutasi = JurnalKasMutasiRepo::getById($data['kas_mutasi_id']);
            $this->rollback($jurnalMutasi);
            $jurnalMutasi = JurnalKasMutasiRepo::update($data);
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>"Jurnal dengan Kode $jurnalMutasi->kode berhasil disimpan"
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
            $kasMutasi = JurnalKasMutasiRepo::getById($id);
            // rollback
            $this->rollback($kasMutasi);
            $kasMutasi->delete();
            DB::commit();
            return [
                'status'=>true,
                'keterangan'=>"Data $kasMutasi->kode Berhasil dihapus"
            ];
        } catch (ModelNotFoundException $e){
            DB::rollBack();
            return [
                'status'=>false,
                'keterangan'=>$e->getMessage()
            ];
        }
    }

    protected function jurnal(KasMutasi $kasMutasi)
    {
        $jurnalTransaksi = JurnalTransaksiRepo::build($kasMutasi);
        foreach ($kasMutasi->kasMutasiDetail as $item) {
            // kas debet
            if ($item->jenis == 'masuk'){
                $jurnalTransaksi->debet($item->akun_kas_id, $item->nominal_masuk);
                NeracaSaldoRepository::debet($item->akun_kas_id, $item->nominal_masuk);
            }
            // kas kredit
            if ($item->jenis == 'keluar'){
                $jurnalTransaksi->debet($item->akun_kas_id, $item->nominal_keluar);
                NeracaSaldoRepository::debet($item->akun_kas_id, $item->nominal_keluar);
            }
        }
    }

    /**
     * @throws ModelNotFoundException
     */
    protected function rollback(KasMutasi $kasMutasi)
    {
        JurnalKasMutasiRepo::rollback($kasMutasi->id);
        $this->rollbackJurnalAndSaldo($kasMutasi);
    }
}
