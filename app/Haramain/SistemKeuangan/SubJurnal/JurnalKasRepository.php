<?php namespace App\Haramain\SistemKeuangan\SubJurnal;

use App\Haramain\SistemKeuangan\SubNeraca\SaldoKasRepository;
use App\Models\Keuangan\JurnalKas;
use App\Models\Keuangan\PenerimaanLain;
use App\Models\Keuangan\PenerimaanPenjualan;
use App\Models\Keuangan\PengeluaranLain;
use App\Models\Keuangan\PengeluaranPembelian;
use App\Models\Keuangan\PiutangInternal;

class JurnalKasRepository
{
    public static function storeForPenerimaanPenjualan(PenerimaanPenjualan $penerimaanPenjualan)
    {
        $getPayment = $penerimaanPenjualan->paymentable()->get();
        foreach ($getPayment as $payment) {
            JurnalKas::create([
                'kode'=>$penerimaanPenjualan->kode,
                'active_cash'=>$penerimaanPenjualan->active_cash,
                'type'=>'debet',
                'jurnal_type'=>$penerimaanPenjualan::class,
                'jurnal_id'=>$penerimaanPenjualan->id,
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
                'type' => 'kredit',
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

    public static function storeForPiutangInternal(PiutangInternal $piutangInternal)
    {
        $getPayment = $piutangInternal->paymentable;
        foreach ($getPayment as $payment) {
            JurnalKas::create([
                'kode'=>$piutangInternal->kode,
                'active_cash'=>$piutangInternal->active_cash,
                'type'=>($piutangInternal->jenis_piutang == 'penerimaan') ? 'debet' : 'kredit', // debet or kredit (enum)
                'jurnal_type'=>$piutangInternal::class,
                'jurnal_id'=>$piutangInternal->id,
                'akun_id'=>$payment->akun_id,
                'nominal_debet'=>($piutangInternal->jenis_piutang == 'penerimaan') ? $payment->nominal : null,
                'nominal_kredit'=>($piutangInternal->jenis_piutang == 'penerimaan') ? null : $payment->nominal,
            ]);
            $type = ($piutangInternal->jenis_piutang == 'penerimaan') ? 'increment' : 'decrement';
            SaldoKasRepository::update($payment->akun_id, $payment->nominal, $type);
        }
    }

    public static function rollbackForPiutangInternal(PiutangInternal $piutangInternal)
    {
        // rollback saldo kas
        foreach ($piutangInternal->jurnalKas as $jurnalKas) {
            if ($piutangInternal->jenis_piutang == 'penerimaan'){
                SaldoKasRepository::rollback($jurnalKas->akun_id, $jurnalKas->nominal_debet, 'decrement');
            }
            if ($piutangInternal->jenis_piutang == 'pengeluaran'){
                SaldoKasRepository::rollback($jurnalKas->akun_id, $jurnalKas->nominal_kredit, 'increment');
            }
        }
        return $piutangInternal->jurnalKas()->delete();
    }

    public static function storeForPenerimaanLain(PenerimaanLain $penerimaanLain)
    {
        $getPayment = $penerimaanLain->paymentable;
        foreach ($getPayment as $payment){
            JurnalKas::create([
                'kode'=>$penerimaanLain->kode,
                'active_cash' => $penerimaanLain->active_cash,
                'type' => 'debet',
                'jurnal_type' => $penerimaanLain::class,
                'jurnal_id' => $penerimaanLain->id,
                'akun_id' => $payment->akun_id,
                'nominal_debet' => $payment->nominal
            ]);
            SaldoKasRepository::update($payment->akun_id, $payment->nominal, 'increment');
        }
    }

    public static function rollbackForPenerimaanLain(PenerimaanLain $penerimaanLain)
    {
        foreach ($penerimaanLain->jurnalKas as $jurnalKas){
            SaldoKasRepository::rollback($jurnalKas->akun_id, $jurnalKas->nominal_debet, 'decrement');
        }
        return $penerimaanLain->jurnalKas()->delete();
    }

    public static function storeForPengeluaranLain(PengeluaranLain $pengeluaranLain)
    {
        $getPayment = $pengeluaranLain->paymentable;
        foreach ($getPayment as $payment){
            JurnalKas::create([
                'kode' => $pengeluaranLain->kode,
                'active_cash' => $pengeluaranLain->active_cash,
                'type' => 'kredit',
                'jurnal_type' => $pengeluaranLain::class,
                'jurnal_id' => $pengeluaranLain->id,
                'akun_id' => $payment->akun_id,
                'nominal_kredit' => $payment->nominal
            ]);
        }
        SaldoKasRepository::update($payment->akun_id, $payment->nominal, 'decrement');
    }

    public static function rollbackForPengeluaranLain(PengeluaranLain $pengeluaranLain)
    {
        foreach ($pengeluaranLain->jurnalKas as $jurnalKas){
            SaldoKasRepository::rollback($jurnalKas->akun_id, $jurnalKas->nominal_kredit, 'increment');
        }
        return $pengeluaranLain->jurnalKas()->delete();
    }
}
