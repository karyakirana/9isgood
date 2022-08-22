<?php namespace App\Haramain\Repository\Neraca;

use App\Models\Keuangan\SaldoHutangPembelian;

class SaldoHutangPembelianRepo
{
    public $saldoHutangPembelian;

    public function __construct()
    {
        $this->saldoHutangPembelian = new SaldoHutangPembelian();
    }

    protected function query($supplierId)
    {
        return $this->saldoHutangPembelian->newQuery()
            ->where('supplier_id', $supplierId);
    }

    public function saldoIncrement($supplierId, $nominal)
    {
        if ($this->query($supplierId)->exists()){
            return $this->query($supplierId)->increment('saldo', $nominal);
        }
        return $this->saldoHutangPembelian->newQuery()
            ->create([
                'supplier_id'=>$supplierId,
                'saldo'=>$nominal
            ]);
    }

    public function saldoDecrement($supplierId, $nominal)
    {
        return $this->query($supplierId)->decrement('saldo', $nominal);
    }
}
