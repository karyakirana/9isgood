<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Haramain\SistemKeuangan\SubNeraca\SaldoKasRepository;
use App\Models\Keuangan\JurnalKas;
use App\Models\Keuangan\PenerimaanPenjualan;
use App\Models\Keuangan\PengeluaranPembelian;

class JurnalKasRepository
{
    public static function storeForPenerimaanPenjualan(PenerimaanPenjualan $penerimaanPenjualan)
    {
        $getPayment = $penerimaanPenjualan->paymentable()->get();
        foreach ($getPayment as $payment) {
            JurnalKas::create([
                'kode'=>$penerimaanPenjualan->kode,
                'active_cash'=>$penerimaanPenjualan->active_cash,
                'type'=>'masuk',
                'cash_type'=>$penerimaanPenjualan::class,
                'cash_id'=>$penerimaanPenjualan->id,
                'akun_id'=>$payment->akun_id,
                'nominal_debet'=>$payment->nominal,
                'nominal_kredit'=>null,
            ]);
            SaldoKasRepository::update($payment->akun_id, $payment->nominal, 'increment');
        }
    }

    public static function rollbackForPenerimaanPenjualan(PenerimaanPenjualan $penerimaanPenjualan)
    {
        // rollback saldo kas
        foreach ($penerimaanPenjualan->jurnalKas as $jurnalKas) {
            SaldoKasRepository::rollback($jurnalKas->akun_id, $jurnalKas->nominal_debet, 'decrement');
        }
        return $penerimaanPenjualan->jurnalKas()->delete();
    }

    public static function storeForPengeluaranPembelian(PengeluaranPembelian $pengeluaranPembelian)
    {
        $getPayment = $pengeluaranPembelian->paymentable()->get();
        foreach ($getPayment as $payment) {
            JurnalKas::create([
                'kode'=>$pengeluaranPembelian->kode,
                'active_cash' => $pengeluaranPembelian->active_cash,
                'type' => 'keluar',
                'cash_type' => $pengeluaranPembelian::class,
                'cash_id' => $pengeluaranPembelian->id,
                'akun_id' => $payment->akun_id,
                'nominal_debet' => null,
                'nominal_kredit' => $payment->nominal
            ]);
            SaldoKasRepository::update($payment->akun_id, $payment->nominal, 'decrement');
        }
    }

    public static function rollbackForPengeluaranPembelian(PengeluaranPembelian $pengeluaranPembelian)
    {
        // rollback saldo kas
        foreach ($pengeluaranPembelian->jurnalKas as $jurnalKas) {
            SaldoKasRepository::rollback($jurnalKas->akun_id, $jurnalKas->nominal_kredit, 'increment');
        }
        return $pengeluaranPembelian->jurnalKas()->delete();
    }
}
