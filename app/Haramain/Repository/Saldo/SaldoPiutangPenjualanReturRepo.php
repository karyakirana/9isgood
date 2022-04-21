<?php namespace App\Haramain\Repository\Saldo;

use App\Models\Keuangan\SaldoPiutangPenjualanRetur;

class SaldoPiutangPenjualanReturRepo
{
    public function find($customer_id)
    {
        $saldoPiutang = SaldoPiutangPenjualanRetur::query()->where('customer_id', $customer_id);
        if ($saldoPiutang->doesntExist()){
            //dd($saldoPiutang);
            return SaldoPiutangPenjualanRetur::query()->create([
                'customer_id'=>$customer_id,
                'saldo'=>0
            ]);
        }
        return $saldoPiutang->first();
    }
}
