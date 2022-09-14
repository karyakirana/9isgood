<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\PembelianTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HutangPembelian extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'hutang_pembelian';
    protected $fillable = [
        'saldo_hutang_pembelian_id',
        'pembelian_type',
        'pembelian_id',
        'status_bayar', // lunas, belum, kurang
        'total_bayar',
        'kurang_bayar',
    ];

    public function saldo_hutang_pembelian()
    {
        return $this->belongsTo(SaldoHutangPembelian::class, 'saldo_hutang_pembelian_id', 'supplier_id');
    }

    public function hutangablePembelian()
    {
        return $this->morphTo(__FUNCTION__, 'pembelian_id', 'pembelian_type');
    }
}
