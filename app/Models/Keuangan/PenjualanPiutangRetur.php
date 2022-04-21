<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanPiutangRetur extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.penjualan_piutang_retur';
    protected $fillable = [
        'saldo_piutang_penjualan_retur_id',
        'jurnal_set_retur_penjualan_id',
        'status_bayar',
        'kurang_bayar'
    ];

    public function saldoPiutangPenjualanRetur()
    {
        return $this->belongsTo(SaldoPiutangPenjualanRetur::class, 'saldo_piutang_penjualan_retur_id', 'saldo_piutang_penjualan_retur_id'. 'customer_id');
    }

    public function jurnalSetReturPenjualan()
    {
        return $this->belongsTo(JurnalSetReturPenjualanAwal::class, 'jurnal_set_retur_penjualan_id');
    }
}
