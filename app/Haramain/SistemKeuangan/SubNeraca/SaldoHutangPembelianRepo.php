<?php namespace App\Haramain\SistemKeuangan\SubNeraca;

use App\Models\Keuangan\SaldoHutangPembelian;

class SaldoHutangPembelianRepo
{
    protected function query($supplierId)
    {
        return SaldoHutangPembelian::query()
            ->where('supplier_id', $supplierId);
    }

    public function saldoIncrement($supplierId, $nominal)
    {
        if ($this->query($supplierId)->exists()){
            return $this->query($supplierId)->increment('saldo', $nominal);
        }
        return SaldoHutangPembelian::query()
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
