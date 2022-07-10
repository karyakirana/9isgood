<?php namespace App\Haramain\Service\SistemKeuangan\Kasir;

use App\Models\Keuangan\PenerimaanPenjualan;

class PenerimaanPenjualanService
{
    // dependency injection
    protected PenerimaanPenjualanRepo $penerimaanPenjualanRepo;

    public function handleRulesValidation():array
    {
        return [];
    }

    public function handleMessagesValidation(): array
    {
        return [];
    }

    public function handleStore($data): object
    {
        // create penerimaan penjualan
        $penerimaanPenjualan = $this->penerimaanPenjualanRepo->store($data);
        // update piutang penjualan
        // update status penjualan
        // create jurnal transaksi
        // create kas debet (update saldo)
        // return id kas masuk
        return (object)[];
    }

    public function handleUpdate()
    {
        // rollback
        // update
    }

    public function handleDestroy()
    {
        //
    }

    public function handleGetData()
    {
        //
    }

    public function handleInitiate()
    {
        //
    }
}
