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
        }
    }

    public static function rollbackForPenerimaanPenjualan(PenerimaanPenjualan $penerimaanPenjualan)
    {
        return $penerimaanPenjualan->jurnalKas()->delete();
    }
}
