<?php

namespace App\Models\Keuangan;

use App\Models\Master\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranPembelian extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.pengeluaran_pembelian';
    protected $fillable = [
        'active_cash',
        'kode',
        'jenis', // INTERNAL atau BLU
        'supplier_id',
        'akun_kas_id',
        'user_id',
        'total_pengeluaran',
        'keterangan'
    ];

    public function akunKas()
    {
        return $this->belongsTo(Akun::class, 'akun_kas_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
