<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Models\Keuangan\JurnalTransaksi;

class JurnalTransaksiRepo
{
    public function getData($jurnalableType, $jurnalableId)
    {
        return JurnalTransaksi::query()
            ->where('jurnal_type', $jurnalableType)
            ->where('jurnal_id', $jurnalableId)
            ->get();
    }

    public function debet($jurnalableType, $jurnalableId, $akunDebetId, $nominal)
    {
        return JurnalTransaksi::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jurnal_type'=>$jurnalableType,
                'jurnal_id'=>$jurnalableId,
                'akun_id'=>$akunDebetId,
                'nominal_debet'=>$nominal,
            ]);
    }

    public function kredit($jurnalableType, $jurnalableId, $akunKreditId, $nominal)
    {
        return JurnalTransaksi::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jurnal_type'=>$jurnalableType,
                'jurnal_id'=>$jurnalableId,
                'akun_id'=>$akunKreditId,
                'nominal_kredit'=>$nominal,
            ]);
    }

    public function rollback($jurnalableType, $jurnalableId)
    {
        return JurnalTransaksi::query()
            ->where('jurnal_type', $jurnalableType)
            ->where('jurnal_id', $jurnalableId)
            ->delete();
    }
}
