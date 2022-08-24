<?php namespace App\Haramain\Repository\Jurnal;

use App\Models\Keuangan\JurnalSetPiutangAwal;

class JurnalSetPiutangAwalRepo
{
    protected $jurnalSetPiutangAwal;

    public function __construct()
    {
        $this->jurnalSetPiutangAwal = new JurnalSetPiutangAwal();
    }

    public function kode()
    {
        $query = $this->jurnalSetPiutangAwal->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->latest('kode');

        // check last num
        if ($query->doesntExist()){
            return '0001/PP/'.date('Y');
        }

        $num = (int)$query->first()->last_num_trans + 1 ;
        return sprintf("%04s", $num)."/PP/".date('Y');
    }

    public function store($data)
    {
        //
    }
}
