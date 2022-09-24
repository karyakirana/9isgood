<?php namespace App\Http\Livewire\Master;

use App\Models\Keuangan\SaldoPiutangPenjualan;
use App\Models\Master\Customer;

trait SetCustomerTrait
{
    public $customer_id, $customer_nama;
    public $customer_diskon;
    public $customer_hutang;
    public $customer_telepon;

    public function setCustomer(Customer $customer)
    {
        $this->customer_id = $customer->id;
        $this->customer_nama = $customer->nama;
        $this->customer_diskon = $customer->diskon;
        $this->customer_telepon = $customer->telepon;
        $saldoHutang = SaldoPiutangPenjualan::find($customer->id);
        $this->customer_hutang = ($saldoHutang) ? $saldoHutang->saldo : 0;
    }
}
