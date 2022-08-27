<?php namespace App\Haramain\Repository\Neraca;

use App\Models\Keuangan\SaldoPiutangPenjualan;

class SaldoPiutangPenjualanRepo
{
    protected $saldoPiutangPenjualan;

    public function __construct()
    {
        $this->saldoPiutangPenjualan = new SaldoPiutangPenjualan();
    }

    protected function query($customerId)
    {
        return $this->saldoPiutangPenjualan->newQuery()->find($customerId);
    }

    protected function create($customerId, $saldo)
    {
        return $this->saldoPiutangPenjualan->newQuery()
            ->create([
                'customer_id'=>$customerId,
                'saldo'=>$saldo
            ]);
    }

    public function increment($customerId, $saldo)
    {
        $query = $this->query($customerId);
        if ($query){
            $query->increment('saldo', $saldo);
            return $query;
        }
        return $this->create($customerId, $saldo);
    }

    public function decrement($customerId, $saldo)
    {
        $query = $this->query($customerId);
        $query->decrement('saldo', $saldo);
        return $query;
    }
}
