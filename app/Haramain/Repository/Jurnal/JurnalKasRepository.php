<?php namespace App\Haramain\Repository\Jurnal;

use App\Models\Keuangan\JurnalKas;

class JurnalKasRepository
{
    protected $jurnalKas;

    public function __construct()
    {
        $this->jurnalKas = new JurnalKas();
    }

    protected function kode()
    {
        $jurnalKas = JurnalKas::query()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');
        if ($jurnalKas->doesntExist()){
            return '1/JKAS/'.date('Y');
        }
        return $jurnalKas->first()->last_num_char +1 .'/JKAS/'.date('Y');
    }

    public function store($data, $jurnalKasType, $jurnalKasId)
    {
        return $this->jurnalKas->newQuery()
            ->create([
                'kode'=>$this->kode(),
                'active_cash'=>session('ClosedCash'),
                'jurnal_type'=>$jurnalKasType,
                'jurnal_id'=>$jurnalKasId,
                'akun_id'=>$data['akunKasId'],
                'nominal_debet'=>$data['nominalDebet'],
                'nominal_kredit'=>$data['nominalKredit'],
            ]);
    }

    public function rollback($jurnalKasType, $jurnalKasId)
    {
        return $this->jurnalKas->newQuery()
            ->where('jurnal_type', $jurnalKasType)
            ->where('jurnal_id', $jurnalKasId)
            ->delete();
    }
}
