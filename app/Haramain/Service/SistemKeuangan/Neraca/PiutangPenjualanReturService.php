<?php namespace App\Haramain\Service\SistemKeuangan\Neraca;

use App\Haramain\Service\SistemKeuangan\Kasir\PiutangPenjualanRepo;
use JetBrains\PhpStorm\Pure;

class PiutangPenjualanReturService
{
    public function __construct()
    {
        //
    }

    public function handleValidation()
    {
        //
    }

    public function handleStore($data)
    {
        // store piutang awal
        // store piutang penjualan
        // update piutang saldo penjualan
        // store jurnal transaksi
        // update neraca saldo awal
        // update neraca saldo
    }

    public function handleEdit($id)
    {
        //
    }

    public function handleUpdate($data)
    {
        /**
         * rollback
         */
        // rollback piutang penjualan
        // rollback piutang saldo penjualan
        // delete jurnal transaksi
        // rollback neraca saldo awal
        // rollback neraca saldo

        /**
         * update
         */
        // update piutang awal
        // update piutang saldo penjualan
        // update neraca saldo awal
        // update neraca saldo

        /**
         * store
         */
        // store piutang penjualan
        // store jurnal transaksi
    }

    public function handleDestroy($id)
    {
        //
    }
}
