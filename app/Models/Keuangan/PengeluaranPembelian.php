<?php

namespace App\Models\Keuangan;

use App\Models\Master\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranPembelian extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.pengeluaran_pembelian';
    protected $fillable = [
        'active_cash',
        'kode',
        'tgl_pengeluaran',
        'jenis', // INTERNAL atau BLU
        'supplier_id',
        'user_id',
        'total_pengeluaran',
        'keterangan'
    ];

    public function tglPengeluaran():Attribute
    {
        return Attribute::make(
            get: fn($value) => tanggalan_format($value),
            set: fn($value) => tanggalan_database_format($value, 'd-M-Y')
        );
    }

    public function pengeluaranPembelianDetail()
    {
        return $this->hasMany(PengeluaranPembelianDetail::class, 'pengeluaran_pembelian_id');
    }

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

    public function payementable()
    {
        return $this->morphMany(Payment::class, 'paymentable', 'paymentable_type', 'paymentable_id');
    }
}
