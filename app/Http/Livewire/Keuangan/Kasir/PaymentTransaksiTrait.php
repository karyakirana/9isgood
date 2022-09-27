<?php namespace App\Http\Livewire\Keuangan\Kasir;

trait PaymentTransaksiTrait
{
    public $dataPayment = [];

    public function addPayment()
    {
        $this->dataPayment[] = [
            'akun_id'=>'',
            'nominal'=>0
        ];
    }

    public $total_dibayar;

    public function setTotalDibayar()
    {
        $this->total_dibayar = array_sum(array_column($this->dataDetail, 'nominal_dibayar'));
        $this->customer_saldo = $this->customer_saldo - $this->total_dibayar;
    }

    public function deletePayment($index)
    {
        unset($this->dataPayment[$index]);
        $this->dataPayment = array_values($this->dataPayment);
    }

    public function setPayment()
    {
        $this->validate([
            'dataPayment.*.akun_id'=>'required',
            'dataPayment.*.nominal'=>'required|gt:0'
        ]);
    }
}
