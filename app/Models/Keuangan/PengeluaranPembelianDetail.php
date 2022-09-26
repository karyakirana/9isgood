<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranPembelianDetail extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.pengeluaran_pembelian_detail';
    protected $fillable = [
        'pengeluaran_pembelian_id',
        'hutang_pembelian_id',
        'nominal_dibayar',
        'kurang_bayar'
    ];

    public function pengeluaranPembelian()
    {
        return $this->belongsTo(PengeluaranPembelian::class, 'pengeluaran_pembelian_id');
    }

    public function hutangPembelian()
    {
        return $this->belongsTo(HutangPembelian::class, 'hutang_pembelian_id');
    }
}
