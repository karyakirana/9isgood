<?php namespace App\Haramain\Service\SistemPenjualan\SubPenjualan;

class PenjualanService
{
    public function handleRulesValidation():array
    {
        return [];
    }

    public function handleMessagesValidation(): array
    {
        return [];
    }

    public function handleStore($data)
    {
        // transaction
        // create penjualan
        // create stock keluar
        // create jurnal piutang
        // create jurnal transaksi
    }

    public function handleUpdate($data)
    {
        // transaction
        // initiate
        // rollback
        // update penjualan
        // update stock keluar
        // update jurnal piutang
        // update jurnal transaksi
    }

    public function handleDestroy($penjualan_id)
    {
        //
    }
}
