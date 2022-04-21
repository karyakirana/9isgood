<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\CustomerTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoPiutangPenjualanRetur extends Model
{
    use HasFactory, CustomerTraits;
    protected $table = 'haramain_keuangan.saldo_piutang_penjualan_retur';
    protected $fillable = [
        'customer_id',
        'saldo',
    ];

    public function penjualan_piutang_retur()
    {
        return $this->hasMany(PenjualanPiutangRetur::class, 'saldo_piutang_penjualan_retur_id', 'customer_id');
    }
}
