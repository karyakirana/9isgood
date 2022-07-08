<?php namespace App\Haramain\Service\SistemKeuangan\Neraca;

use App\Models\Keuangan\NeracaSaldo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait NeracaSaldoTrait
{
    public function updateNeracaSaldoDebet($akunId, $nominal): Model|Builder|int
    {
        $builder = NeracaSaldo::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('akun_id', $akunId);
        if ($builder->doesntExist()){
            return NeracaSaldo::query()
                ->create([
                    'active_cash'=>session('ClosedCash'),
                    'akun_id'=>$akunId,
                    'debet'=>$nominal
                ]);
        }
        return $builder->increment('debet', $nominal);
    }

    public function rollbackNeracaSaldoDebet($akunId, $nominal): Model|Builder|int
    {
        return NeracaSaldo::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('akun_id', $akunId)
            ->decrement('debet', $nominal);
    }

    public function updateNeracaSaldoKredit($akunId, $nominal): Model|Builder|int
    {
        $builder = NeracaSaldo::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('akun_id', $akunId);
        if ($builder->doesntExist()){
            return NeracaSaldo::query()
                ->create([
                    'active_cash'=>session('ClosedCash'),
                    'akun_id'=>$akunId,
                    'kredit'=>$nominal
                ]);
        }
        return $builder->increment('kredit', $nominal);
    }

    public function rollbackNeracaSaldoKredit($akunId, $nominal): Model|Builder|int
    {
        return NeracaSaldo::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('akun_id', $akunId)
            ->decrement('kredit', $nominal);
    }
}
