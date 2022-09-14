<?php namespace App\Haramain\SistemKeuangan\SubNeraca;

use App\Models\Keuangan\SaldoKas;
use function PHPUnit\Framework\isNull;

class SaldoKasRepository
{
    public function getById($akunKasId)
    {
        return SaldoKas::query()->findOrFail($akunKasId);
    }

    public function increment($akunKasId, $nominal)
    {
        $query = SaldoKas::query()->find($akunKasId);
        if (isNull($query)){
            return SaldoKas::query()->create([
                'akun_kas_id'=>$akunKasId,
                'saldo'=>$nominal
            ]);
        }
        return $query->increment('saldo', $nominal);
    }

    public function decrement($akunKasId, $nominal)
    {
        $query = SaldoKas::query()->find($akunKasId);
        if (isNull($query)){
            return SaldoKas::query()->create([
                'akun_kas_id'=>$akunKasId,
                'saldo'=>0 - $nominal
            ]);
        }
        return $query->decrement('saldo', $nominal);
    }
}
