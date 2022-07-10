<?php namespace App\Haramain\Service\SistemKeuangan\Jurnal;

use App\Models\Keuangan\JurnalKas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KasRepo
{
    public function kode()
    {
        return null;
    }

    public function getById()
    {
        //
    }

    public function getBySession()
    {
        //
    }

    public function store($jurnalType, $jurnalId, $data): Model|Builder
    {
        return JurnalKas::query()
            ->create([
                'kode'=>$this->kode(),
                'active_cash'=>session('ClosedCash'),
                'type'=>$data->type,
                'jurnal_type'=>$jurnalType,
                'jurnal_id'=>$jurnalId,
                'akun_id'=>$data->akun_id,
                'nominal_debet'=>$data->nominal_debet,
                'nominal_kredit'=>$data->nominal_kredit,
                'nominal_saldo'=>$data->nominal_saldo,
            ]);
    }
}
