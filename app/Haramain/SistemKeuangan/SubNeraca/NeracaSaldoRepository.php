<?php namespace App\Haramain\SistemKeuangan\SubNeraca;

use App\Models\Keuangan\Akun;
use App\Models\Keuangan\NeracaSaldo;

class NeracaSaldoRepository
{
    private static function create($akunId, $typeAkun, $field, $nominal)
    {
        $nominal = ($typeAkun == $field) ? $nominal : 0 - $nominal;
        return NeracaSaldo::query()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'akun_id'=>$akunId,
                'type'=>$typeAkun,
                $field => $nominal,
            ]);
    }

    private static function query($akunId)
    {
        return NeracaSaldo::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('akun_id', $akunId);
    }

    private static function getAkunType($akunId)
    {
        return Akun::query()->find($akunId)->akunTipe->default_saldo;
    }

    public static function debet($akunId, $nominal)
    {
        $neracaSaldo = self::query($akunId)->first();
        $akunType = self::getAkunType($akunId);
        if ($neracaSaldo == null){
            // create
            return self::create($akunId, $akunType, 'debet', $nominal);
        }
        // update
        if ($akunType == 'debet'){
            return $neracaSaldo->increment($akunType, $nominal);
        }
        return $neracaSaldo->decrement($akunType, $nominal);
    }

    public static function kredit($akunId, $nominal)
    {
        $neracaSaldo = self::query($akunId)->first();
        $akunType = self::getAkunType($akunId);
        if ($neracaSaldo == null){
            // create
            return self::create($akunId, $akunType, 'kredit', $nominal);
        }
        // update
        if ($akunType == 'kredit'){
            return $neracaSaldo->increment($akunType, $nominal);
        }
        return $neracaSaldo->decrement($akunType, $nominal);
    }

    public static function debetRollback($akunId, $nominal)
    {
        $neracaSaldo = self::query($akunId)->first();
        $akunType = self::getAkunType($akunId);
        if ($akunType == 'debet'){
            return $neracaSaldo->decrement($akunType, $nominal);
        }
        return $neracaSaldo->increment($akunType, $nominal);
    }

    public static function kreditRollback($akunId, $nominal)
    {
        $neracaSaldo = self::query($akunId)->first();
        $akunType = self::getAkunType($akunId);
        if ($akunType == 'kredit'){
            return $neracaSaldo->decrement($akunType, $nominal);
        }
        return $neracaSaldo->increment($akunType, $nominal);
    }

    public function cleanupByAkunId($akunId)
    {
        return NeracaSaldo::query()
            ->where('akun_id', $akunId)
            ->where('active_cash', session('ClosedCash'))
            ->delete();
    }
}
