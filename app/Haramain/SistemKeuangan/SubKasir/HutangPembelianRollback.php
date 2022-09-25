<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Models\Keuangan\PengeluaranPembelianDetail;

class HutangPembelianRollback
{
    public static function fromPengeluaranPembelian(PengeluaranPembelianDetail $pengeluaranPembelianDetail)
    {
        $hutangPembelian = $pengeluaranPembelianDetail->hutangPembelian;
        $totalbayar = $hutangPembelian->hutangablePembelian->total_bayar;
        $jumlahBayar = $pengeluaranPembelianDetail->nominal_dibayar + $pengeluaranPembelianDetail->kurang_bayar;
        $statusBayar = ($totalbayar === abs($jumlahBayar)) ? 'belum' : 'kurang';
        // rollback status bayar pembelian atau pembelian retur
        $hutangPembelian->hutangablePembelian()->update(['status_bayar'=>$statusBayar]);
        // rollback status bayar hutang pembelian
        $hutangPembelian->update([
            'status_bayar' => $statusBayar,
            'kurang_bayar' => $pengeluaranPembelianDetail->nominal_dibayar + $hutangPembelian->kurang_bayar
        ]);
    }
}
