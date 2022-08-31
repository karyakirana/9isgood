<?php namespace App\Haramain\Repository\Neraca;

use App\Models\Keuangan\SaldoHutangPembelian;

class SaldoHutangPembelianRepo
{
    protected $saldoHutangPembelian;
    protected $supplierId;

    public function __construct($supplierId)
    {
        $this->saldoHutangPembelian = new SaldoHutangPembelian();
        $this->supplierId = $supplierId;
    }

    protected function query()
    {
        return SaldoHutangPembelian::query()
            ->where('supplier_id', $this->supplierId);
    }

    public function saldoIncrement($nominal)
    {
        if ($this->query()->exists()){
            return $this->query()->increment('saldo', $nominal);
        }
        return $this->saldoHutangPembelian->newQuery()
            ->create([
                'supplier_id'=>$this->supplierId,
                'saldo'=>$nominal
            ]);
    }

    public function saldoDecrement($nominal)
    {
        return $this->query()->decrement('saldo', $nominal);
    }
}
