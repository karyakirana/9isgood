<?php namespace App\Haramain\Service\SistemKeuangan\Jurnal;

use App\Models\Keuangan\JurnalKas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KasRepo
{
    public function kode($type): string
    {
        $initial = ($type == 'masuk') ? 'KM' : 'KK';
        $query = JurnalKas::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('type', $type)
            ->latest('kode');
        // check last num
        if ($query->doesntExist()) {
            return '00001/' .$initial.'/'.date('Y');
        }
        $num = (int)$query->first()->last_num_char + 1 ;
        return sprintf("%05s", $num) . "/".$initial."/" . date('Y');
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
                'kode'=>$this->kode($data->jenis_kas),
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
