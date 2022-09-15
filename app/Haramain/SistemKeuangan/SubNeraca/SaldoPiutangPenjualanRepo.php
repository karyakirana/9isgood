<?php namespace App\Haramain\SistemKeuangan\SubNeraca;

use App\Models\Keuangan\SaldoPiutangPenjualan;

class SaldoPiutangPenjualanRepo
{
    protected $customerId, $saldo;

    public function __construct($customerId, $saldo)
    {
        $this->customerId = $customerId;
        $this->saldo = $saldo;
    }

    public static function build($customerId, $saldo)
    {
        return new static($customerId, $saldo);
    }

    public function getDataById()
    {
        return SaldoPiutangPenjualan::query()->find($this->customerId);
    }

    public function penjualan()
    {
        $query = $this->getDataById();
        if ($query){
            $query->increment('saldo', $this->saldo);
            return $query;
        }
        return $this->create();
    }

    public function retur()
    {
        $query = $this->getDataById();
        if ($query){
            $query->decrement('saldo', $this->saldo);
            return $query;
        }
        $this->saldo = 0 - $this->saldo;
        return $this->create();
    }

    protected function create()
    {
        return SaldoPiutangPenjualan::query()
            ->create([
                'customer_id'=>$this->customerId,
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
