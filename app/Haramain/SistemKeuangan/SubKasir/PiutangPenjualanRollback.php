<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Models\Keuangan\PenerimaanPenjualanDetail;

class PiutangPenjualanRollback
{
    public static function fromPenerimaanPenjualan(PenerimaanPenjualanDetail $penerimaanPenjualanDetail)
    {
        $piutangPenjualan = $penerimaanPenjualanDetail->piutangPenjualan;
        $totalBayar = $piutangPenjualan->piutangablePenjualan->total_bayar;
        $jumlahBayar = $penerimaanPenjualanDetail->nominal_dibayar + $penerimaanPenjualanDetail->kurang_bayar;
        $statusBayar = ($totalBayar == abs($jumlahBayar)) ? 'belum' : 'kurang';
        // rollback status bayar penjualan atau penjualan retur
        $piutangPenjualan->piutangablePenjualan()->update(['status_bayar'=>$statusBayar]);
        // rollback status bayar piutang penjualan
        $piutangPenjualan->update([
            'status_bayar' => $statusBayar,
            'kurang_bayar' => $penerimaanPenjualanDetail->nominal_dibayar + $piutangPenjualan->kurang_bayar
        ]);
    }
}
