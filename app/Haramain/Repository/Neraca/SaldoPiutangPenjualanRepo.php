<?php namespace App\Haramain\Repository\Neraca;

use App\Models\Keuangan\SaldoPiutangPenjualan;

class SaldoPiutangPenjualanRepo
{
    protected $customerId;

    public function __construct($customerId)
    {
        $this->customerId = $customerId;
    }

    protected function query()
    {
        return SaldoPiutangPenjualan::query()->find($this->customerId);
    }

    protected function create($nominal)
    {
        return SaldoPiutangPenjualan::query()
            ->create([
                'customer_id'=>$this->customerId,
                'saldo'=>$nominal
            ]);
    }

    public function increment($nominal)
    {
        $query = $this->query($this->customerId);
        if ($query){
            $query->increment('saldo', $nominal);
            return $query;
        }
        return $this->create($nominal);
    }

    public function decrement($nominal)
    {
        $query = $this->query();
        $query->decrement('saldo', $nominal);
        return $query;
    }
}
