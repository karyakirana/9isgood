<?php namespace App\Haramain\Service\SistemKeuangan;

use App\Models\Keuangan\NeracaSaldo;

class NeracaSaldoRepository
{
    protected $neracaSaldo;

    public function __construct()
    {
        $this->neracaSaldo = new NeracaSaldo();
    }

    public function handleStoreDebet($akunDebetId, $nominal)
    {
        $query = $this->neracaSaldo->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where('akun_id', $akunDebetId);
        if ($query->exists()){
            return $query->increment('nominal_debet', $nominal);
        }
        return $this->createDebet($akunDebetId, $nominal);
    }

    public function handleStoreKredit($akunKreditId, $nominal)
    {
        $query = $this->neracaSaldo->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where('akun_id', $akunKreditId);
        if ($query->exists()){
            return $query->increment('nominal_kredit', $nominal);
        }
        return $this->createDebet($akunKreditId, $nominal);
    }

    protected function createDebet($akunDebetId, $nominal)
    {
        return $this->neracaSaldo->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'akun_id'=>$akunDebetId,
                'debet'=>$nominal,
            ]);
    }

    protected function createKredit($akunDebetId, $nominal)
    {
        return $this->neracaSaldo->newQuery()
            ->create([
                'active_cash'=>session('ClosedCash'),
                'akun_id'=>$akunDebetId,
                'debet'=>$nominal,
            ]);
    }
}
