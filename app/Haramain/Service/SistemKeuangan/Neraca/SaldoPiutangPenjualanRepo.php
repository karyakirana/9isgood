<?php namespace App\Haramain\Service\SistemKeuangan\Neraca;

use App\Models\Keuangan\SaldoPiutangPenjualan;
use Illuminate\Database\Eloquent\Collection;

class SaldoPiutangPenjualanRepo
{
    public function store($customer_id, $type, $nominal): Collection|bool|int|array|null
    {
        $nominal = ($type == 'penjualan') ? $nominal : 0 - $nominal;
        $saldoPiutangPenjualan = SaldoPiutangPenjualan::query()->find($customer_id);
        if ($saldoPiutangPenjualan){
            return $saldoPiutangPenjualan->increment($nominal);
        }
        return $saldoPiutangPenjualan->create([
            'customer_id'=>$customer_id,
            'saldo'=>$nominal
        ]);
    }

    public function rollback($customer_id, $type, $nominal): bool|int
    {
        $nominal = ($type == 'penjualan') ? $nominal : 0 - $nominal;
        $saldoPiutangPenjualan = SaldoPiutangPenjualan::query()->find($customer_id);
        return $saldoPiutangPenjualan->decrement($nominal);
    }
}
