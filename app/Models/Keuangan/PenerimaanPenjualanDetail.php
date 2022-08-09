<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanPenjualanDetail extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.penerimaan_penjualan_detail';
    protected $fillable = [
        'penerimaan_penjualan_id',
        'piutang_penjualan_id',
        'nominal_dibayar',
        'kurang_bayar',
    ];

    public function penerimaanPenjualan()
    {
        return $this->belongsTo(PenerimaanPenjualan::class, 'penerimaan_penjualan_id');
    }
}
