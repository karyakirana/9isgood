<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Models\Keuangan\KasMutasi;
use App\Models\Keuangan\KasMutasiDetail;
use Exception;

class JurnalKasMutasiRepo
{
    protected $jurnalKas;

    public function __construct()
    {
        $this->jurnalKas = new JurnalKasRepository();
    }

    public function getById($id)
    {
        return KasMutasi::query()->findOrFail($id);
    }

    protected function kode()
    {
        return null;
    }

    public function store($data)
    {
        $data = (object) $data;
        $kasMutasi = KasMutasi::query()->create([
            'kode'=>$this->kode(),
            'user_id'=>$data->userId,
            'total_mutasi'=>$data->totalMutasi,
            'keterangan'=>$data->keterangan
        ]);
        $this->storeDetail($data->dataDetail, $kasMutasi);
        return $kasMutasi;
    }

    /**
     * @throws Exception
     */
    public function update($data)
    {
        $data = (object)$data;
        $kasMutasi = $this->getById($data->kasMutasiId);
        $this->rollback($data->kasMutasiId);
        $kasMutasi->update([
            'user_id'=>$data->userId,
            'total_mutasi'=>$data->totalMutasi,
            'keterangan'=>$data->keterangan
        ]);
        $kasMutasi = $this->getById($data->kasMutasiId); // refresh query
        $this->storeDetail($data->dataDetail, $kasMutasi);
        return $kasMutasi;
    }

    protected function storeDetail($dataDetail, $kasMutasi)
    {
        foreach ($dataDetail as $item) {
            $item = (object) $item;
            $kasMutasiDetail = KasMutasiDetail::query()->create([
                'kas_mutasi_id'=>$kasMutasi->id,
                'jenis'=>$item->jenis, // masuk atau keluar
                'akun_kas_id'=>$item->akun_kas_id,
                'nominal_masuk'=>$item->nominal_masuk,
                'nominal_keluar'=>$item->nominal_keluar,
            ]);
            // store jurnal kas
            $type = ($item->jenis == 'masuk') ? 'debet' : 'kredit';
            $dataStoreKas = [
                'akunKasId'=>$item->akun_kas_id,
                'totalTransaksiKas'=>($item->jenis == 'masuk') ? $item->nominal_masuk : $item->nominal_keluar
            ];
            $this->jurnalKas->store($dataStoreKas, $type, $kasMutasiDetail::class, $kasMutasiDetail->id);
        }
    }

    /**
     * @throws Exception
     */
    public function rollback($kasMutasiId)
    {
        // get mutasi
        $kasMutasi = KasMutasi::query()->find($kasMutasiId);
        if ($kasMutasi == null){
            throw new Exception('Data Kas Mutasi Tidak Ada');
        }
        // get detail
        $kasMutasiDetail = KasMutasiDetail::query()->where('kas_mutasi_id', $kasMutasi->id);
        if ($kasMutasiDetail->count() > 0){
            // each detail
            foreach ($kasMutasiDetail->get() as $kasMutasiDetail) {
                // rollback kas repository
                $this->jurnalKas->destroy($kasMutasiDetail::class, $kasMutasiDetail->id);
            }
        }
        return $kasMutasiDetail->delete();
    }
}
