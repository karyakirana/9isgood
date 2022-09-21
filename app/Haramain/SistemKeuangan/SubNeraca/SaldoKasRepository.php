<?php namespace App\Haramain\SistemKeuangan\SubNeraca;

use App\Models\Keuangan\SaldoKas;
use function PHPUnit\Framework\isNull;

class SaldoKasRepository
{
    public static function update($akunKas, $saldo, $type)
    {
        $type = ($type== 'increment') ? 'increment' : 'decrement';
        $saldoKas = SaldoKas::query()->where('akun_kas_id', $akunKas);
        if ($saldoKas->exists()){
            return $saldoKas->{$type}('saldo', $saldo);
        }
        return SaldoKas::create([
            'akun_kas_id' => $akunKas,
            'saldo' => $saldo
        ]);
    }
}
