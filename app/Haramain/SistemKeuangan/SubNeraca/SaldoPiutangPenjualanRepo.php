<?php namespace App\Haramain\SistemKeuangan\SubNeraca;

use App\Models\Keuangan\SaldoPiutangPenjualan;

class SaldoPiutangPenjualanRepo
{
    public function getDataById($customerId)
    {
        return SaldoPiutangPenjualan::query()->find($customerId);
    }

    public function penjualan($customerId, $nominal)
    {
        $query = $this->getDataById($customerId);
        if ($query){
            $query->increment('saldo', $nominal);
            return $query;
        }
        return $this->create($customerId, $nominal);
    }

    public function retur($customerId, $nominal)
    {
        $query = $this->getDataById($customerId);
        if ($query){
            $query->decrement('saldo', $nominal);
            return $query;
        }
        return $this->create($customerId, $nominal);
    }

    protected function create($customerId, $nominal)
    {
        return SaldoPiutangPenjualan::query()
            ->create([
                'customer_id'=>$customerId,
                'saldo'=>$nominal
            ]);
    }

    public function penjualanRollback($customerId, $nominal)
    {
        return $this->getDataById($customerId)->decrement('saldo', $nominal);
    }

    public function returRollback($customerId, $nominal)
    {
        return $this->getDataById($customerId)->increment('saldo', $nominal);
    }
}
