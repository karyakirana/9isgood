<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Models\Keuangan\KasMutasi;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class JurnalKasMutasiRepo
{
    public static function kode()
    {
        $jurnalKas = KasMutasi::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');
        if ($jurnalKas->doesntExist()){
            return '1/MUTASI/'.date('Y');
        }
        return $jurnalKas->first()->last_num_char +1 .'/MUTASI/'.date('Y');
    }

    public static function getById($id)
    {
        return KasMutasi::query()->find($id);
    }

    protected static function jurnalKasStore(KasMutasi $kasMutasi, array $dataDetail)
    {
        foreach ($dataDetail as $item) {
            $data['kode'] = $kasMutasi->kode;
            $data['active_cash'] = session('ClosedCash');
            $data['akun_id'] = $item['akun_kas_id'];
            if ($item->jenis == 'keluar'){
                $data['type'] = 'kredit';
                $data['nominal_kredit'] = $item['nominal_keluar'];
                $kasMutasi->jurnalKas()->create($data);
            }
            if ($item->jenis == 'masuk'){
                $data['type'] = 'debet';
                $data['nominal_debet'] = $item['nominal_masuk'];
                $kasMutasi->jurnalKas()->create($data);
            }
        }
    }

    public static function store(array $data)
    {
        $data['kode'] = self::kode(); // add kode
        $kasMutasi = KasMutasi::query()->create($data);
        $kasMutasi->kasMutasiDetail()->createMany($data['dataDetail']);
        self::jurnalKasStore($kasMutasi->refresh(), $data['dataDetail']);
        return $kasMutasi->refresh();
    }

    public static function update($data)
    {
        $kasMutasi = self::getById($data['kas_mutasi_id']);
        if ($kasMutasi == null){
            throw new ModelNotFoundException('Data Kas Mutasi Tidak Ada');
        }
        $kasMutasi->update($data);
        $kasMutasi->kasMutasiDetail()->createMany($data['dataDetail']);
        self::jurnalKasStore($kasMutasi, $data['dataDetail']);
        return $kasMutasi;
    }

    public static function rollback($kasMutasiId)
    {
        // get mutasi
        $kasMutasi = self::getById($kasMutasiId);
        if ($kasMutasi == null){
            throw new ModelNotFoundException('Data Kas Mutasi Tidak Ada');
        }
        // get detail
        $kasMutasi->kasMutasiDetail()->delete();
        $kasMutasi->jurnalKas()->delete();
    }
}
