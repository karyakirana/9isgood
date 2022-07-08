<?php namespace App\Haramain\Service\SistemKeuangan\Neraca;

use App\Models\Keuangan\NeracaSaldo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NeracaSaldoRepository
{
    private function builderBase($akunId): Builder
    {
        return NeracaSaldo::query()
            ->where('active_cash', session('ClosedCash'))
            ->where('akun_id', $akunId);
    }

    public function updateDebet($akunId, $nominal): Model|Builder|int
    {
        $builder = $this->builderBase($akunId);
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

    public function updateKredit($akunId, $nominal): Model|Builder|int
    {
        $builder = $this->builderBase($akunId);
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

    public function rollbackDebet($akunId, $nominal): int
    {
        return $this->builderBase($akunId)
            ->decrement('debet', $nominal);
    }

    public function rollbackKredit($akunId, $nominal): int
    {
        return $this->builderBase($akunId)
            ->decrement('debet', $nominal);
    }
}
