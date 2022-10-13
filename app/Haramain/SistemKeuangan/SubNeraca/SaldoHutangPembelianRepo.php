<?php namespace App\Haramain\SistemKeuangan\SubNeraca;

use App\Models\Keuangan\SaldoHutangPembelian;

class SaldoHutangPembelianRepo
{
    protected $supplierId, $saldo;

    public function __construct($supplierId, $saldo)
    {
        $this->supplierId = $supplierId;
        $this->saldo = $saldo;
    }

    public static function build(...$params)
    {
        return new static(...$params);
    }

    public function getDataById()
    {
        return SaldoHutangPembelian::query()->find($this->supplierId);
    }

    public function pembelian()
    {
        $query = $this->getDataById();
        //dd($query->exists());
        if ($query){
            return $query->increment('saldo', $this->saldo);
        }
        return $this->create();
    }

    public function retur()
    {
        $query = $this->getDataById();
        if ($query->exists()){
            return $query->decrement('saldo', $this->saldo);
        }
        $this->saldo = 0 - $this->saldo;
        return $this->create();
    }

    protected function create()
    {
        return SaldoHutangPembelian::query()
            ->create([
                'supplier_id'=>$this->supplierId,
                'saldo'=>$this->saldo
            ]);
    }

    public function penjualanRollback()
    {
        return $this->getDataById()->decrement('saldo', $this->saldo);
    }

    public function returRollback()
    {
        return $this->getDataById()->increment('saldo', $this->saldo);
    }
}
