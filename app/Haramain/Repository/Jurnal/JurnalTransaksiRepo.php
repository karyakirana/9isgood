<?php namespace App\Haramain\Repository\Jurnal;

use App\Models\Keuangan\JurnalTransaksi;

class JurnalTransaksiRepo
{
    protected $jurnalTransaksi;

    public function __construct()
    {
        $this->jurnalTransaksi = new JurnalTransaksi();
    }

    public function getData($jurnalableType, $jurnalableId)
    {
        return $this->jurnalTransaksi->newQuery()
            ->where('jurnal_type', $jurnalableType)
            ->where('jurnal_id', $jurnalableId)
            ->get();
    }

    public function storeDebet($jurnalableType, $jurnalableId, $akunId, $nominal)
    {
        return $this->jurnalTransaksi->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jurnal_type'=>$jurnalableType,
                'jurnal_id'=>$jurnalableId,
                'akun_id'=>$akunId,
                'nominal_debet'=>$nominal,
            ]);
    }

    public function storeKredit($jurnalableType, $jurnalableId, $akunId, $nominal)
    {
        return $this->jurnalTransaksi->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jurnal_type'=>$jurnalableType,
                'jurnal_id'=>$jurnalableId,
                'akun_id'=>$akunId,
                'nominal_kredit'=>$nominal,
            ]);
    }

    public function rollback($jurnalableType, $jurnalableId)
    {
        return $this->jurnalTransaksi->newQuery()
            ->where('jurnal_type', $jurnalableType)
            ->where('jurnal_id', $jurnalableId)
            ->delete();
    }
}
