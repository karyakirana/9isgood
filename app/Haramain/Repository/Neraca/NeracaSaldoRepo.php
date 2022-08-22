<?php namespace App\Haramain\Repository\Neraca;

use App\Models\Keuangan\NeracaSaldo;

class NeracaSaldoRepo
{
    protected $neracaSaldo;

    public function __construct()
    {
        $this->neracaSaldo = new NeracaSaldo();
    }

    protected function query($akunId)
    {
        return $this->neracaSaldo->newQuery()
            ->where('active_cash', session('ClosedCash'))
            ->where('akun_id', $akunId);
    }

    public function debetIncrement($akunId, $nominal)
    {
        $query = $this->query($akunId);
        if ($query->doesntExist()){
            return $this->neracaSaldo->newQuery()
                ->create([
                    'active_cash'=>session('ClosedCash'),
                    'akun_id'=>$akunId,
                    'debet'=>$nominal,
                ]);
        }
        return $query->increment('debet', $nominal);
    }

    public function debetDecrement($akunId, $nominal)
    {
        $query = $this->query($akunId);
        return $query->decrement('debet', $nominal);
    }

    public function kreditIncrement($akunId, $nominal)
    {
        $query = $this->query($akunId);
        if ($query->doesntExist()){
            return $this->neracaSaldo->newQuery()
                ->create([
                    'active_cash'=>session('ClosedCash'),
                    'akun_id'=>$akunId,
                    'kredit'=>$nominal,
                ]);
        }
        return $query->increment('kredit', $nominal);
    }

    public function kreditDecrement($akunId, $nominal)
    {
        $query = $this->query($akunId);
        return $query->decrement('kredit', $nominal);
    }
}
