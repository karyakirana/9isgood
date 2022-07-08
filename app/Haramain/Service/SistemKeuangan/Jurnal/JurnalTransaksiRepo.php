<?php namespace App\Haramain\Service\SistemKeuangan\Jurnal;

use App\Models\Keuangan\JurnalTransaksi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JurnalTransaksiRepo
{
    private function builderGetByMorph($jurnalType, $jurnalId): Builder
    {
        return JurnalTransaksi::query()
            ->where('jurnal_type', $jurnalType)
            ->where('jurnal_id', $jurnalId);
    }

    public function getByDebetRow($jurnalType, $jurnalId): object|null
    {
        return $this->builderGetByMorph($jurnalType, $jurnalId)
            ->whereNotNull('nominal_debet')
            ->first();
    }

    public function getByKreditRow($jurnalType, $jurnalId): object|null
    {
        return $this->builderGetByMorph($jurnalType, $jurnalId)
            ->whereNotNull('nominal_kredit')
            ->first();
    }

    public function createDebet($akunKredit, $jurnalType, $jurnalId, $nominal, $keterangan = null): Model|Builder
    {
        return JurnalTransaksi::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jurnal_type'=>$jurnalType,
                'jurnal_id'=>$jurnalId,
                'akun_id'=>$akunKredit,
                'nominal_kredit'=>$nominal,
                'keterangan'=>$keterangan
            ]);
    }

    public function createKredit($akunKredit, $jurnalType, $jurnalId, $nominal, $keterangan = null): Model|Builder
    {
        return JurnalTransaksi::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'jurnal_type'=>$jurnalType,
                'jurnal_id'=>$jurnalId,
                'akun_id'=>$akunKredit,
                'nominal_kredit'=>$nominal,
                'keterangan'=>$keterangan
            ]);
    }

    public function transaksiRollback($jurnalType, $jurnalId)
    {
        return JurnalTransaksi::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('jurnal_type', $jurnalType)
            ->where('jurnal_id', $jurnalId)
            ->delete();
    }
}
