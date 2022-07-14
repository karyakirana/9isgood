<?php namespace App\Haramain\Traits\LivewireTraits;

use App\Models\Master\Customer;

trait SetCustomerTraits
{
    public $customer_id, $customer_nama, $customer_diskon;

    public function setCustomer($customerId): void
    {
        $customer = Customer::query()->find($customerId);
        $this->customer_id = $customer->id;
        $this->customer_nama = $customer->nama;
        $this->customer_diskon = $customer->diskon;
    }
}
