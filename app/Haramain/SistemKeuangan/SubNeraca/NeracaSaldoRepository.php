<?php namespace App\Haramain\SistemKeuangan\SubNeraca;

use App\Models\Keuangan\Akun;
use App\Models\Keuangan\NeracaSaldo;

class NeracaSaldoRepository
{
    private function create($akunId, $typeAkun, $field, $nominal)
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

    private function query($akunId)
    {
        return NeracaSaldo::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('akun_id', $akunId);
    }

    private function getAkunType($akunId)
    {
        return Akun::query()->find($akunId)->akunTipe->default_saldo;
    }

    public function debet($akunId, $nominal)
    {
        $neracaSaldo = $this->query($akunId)->first();
        $akunType = $this->getAkunType($akunId);
        if ($neracaSaldo == null){
            // create
            return $this->create($akunId, $akunType, 'debet', $nominal);
        }
        // update
        if ($akunType == 'debet'){
            return $neracaSaldo->increment($akunType, $nominal);
        }
        return $neracaSaldo->decrement($akunType, $nominal);
    }

    public function kredit($akunId, $nominal)
    {
        $neracaSaldo = $this->query($akunId)->first();
        $akunType = $this->getAkunType($akunId);
        if ($neracaSaldo == null){
            // create
            return $this->create($akunId, $akunType, 'kredit', $nominal);
        }
        // update
        if ($akunType == 'kredit'){
            return $neracaSaldo->increment($akunType, $nominal);
        }
        return $neracaSaldo->decrement($akunType, $nominal);
    }

    public function debetRollback($akunId, $nominal)
    {
        $neracaSaldo = $this->query($akunId)->first();
        $akunType = $this->getAkunType($akunId);
        if ($akunType == 'debet'){
            return $neracaSaldo->decrement($akunType, $nominal);
        }
        return $neracaSaldo->increment($akunType, $nominal);
    }

    public function kreditRollback($akunId, $nominal)
    {
        $neracaSaldo = $this->query($akunId)->first();
        $akunType = $this->getAkunType($akunId);
        if ($akunType == 'kredit'){
            return $neracaSaldo->decrement($akunType, $nominal);
        }
        return $neracaSaldo->increment($akunType, $nominal);
    }
}
