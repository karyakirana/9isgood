<?php namespace App\Haramain\Traits\LivewireTraits;

use App\Models\Master\Supplier;

trait SetSupplierTraits
{
    public $supplier_id, $supplierNama, $supplier_nama, $supplier_saldo;

    public function setSupplier(Supplier $supplier)
    {
        $this->supplier_id = $supplier->id;
        $this->supplierNama = $supplier->nama;
        $this->supplier_nama = $supplier->nama;
        $this->supplier_saldo = $supplier->saldoHutang->saldo ?: null;
        $this->emit('hideModalSupplier');
    }
}
