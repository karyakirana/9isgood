<?php namespace App\Haramain\Service\SistemKeuangan\Neraca;

use App\Models\Keuangan\SaldoPiutangPenjualan;
use Illuminate\Database\Eloquent\Collection;

class SaldoPiutangPenjualanRepo
{
    public function store($customer_id, $type, $nominal)
    {
        $nominal = ($type == 'penjualan') ? $nominal : 0 - $nominal;
        $saldoPiutangPenjualan = SaldoPiutangPenjualan::query()->find($customer_id);
        if ($saldoPiutangPenjualan){
            return $saldoPiutangPenjualan->increment('saldo', $nominal);
        }
        return SaldoPiutangPenjualan::query()->create([
            'customer_id'=>$customer_id,
            'saldo'=>$nominal
        ]);
    }

    public function rollback($customer_id, $type, $nominal): bool|int
    {
        $nominal = ($type == 'penjualan') ? $nominal : 0 - $nominal;
        $saldoPiutangPenjualan = SaldoPiutangPenjualan::query()->find($customer_id);
        return $saldoPiutangPenjualan->decrement('saldo', $nominal);
    }
}
