<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Models\Keuangan\PenerimaanPenjualanDetail;

class PiutangPenjualanRollback
{
    public static function fromPenerimaanPenjualan(PenerimaanPenjualanDetail $penerimaanPenjualanDetail)
    {
        $piutangPenjualan = $penerimaanPenjualanDetail->piutangPenjualan;
        $totalBayar = $piutangPenjualan->piutangablePenjualan->total_bayar;
        $piutangPenjualanType = class_basename($piutangPenjualan->penjualan_type); // Penjualan or Penjualan Retur
        $jumlahBayar = $penerimaanPenjualanDetail->nominal_dibayar + $penerimaanPenjualanDetail->kurang_bayar;
        $statusBayar = ($totalBayar == abs($jumlahBayar)) ? 'belum' : 'kurang';
        $piutangPenjualan->piutangablePenjualan()->update(['status_bayar'=>$statusBayar]);
        $piutangPenjualan->update([
            'status_bayar' => $statusBayar,
            'kurang_bayar' => $penerimaanPenjualanDetail->nominal_dibayar + $piutangPenjualan->kurang_bayar
        ]);
    }
}
