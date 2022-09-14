<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoKas extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.saldo_kas';
    protected $fillable = [
        'akun_kas_id',
        'saldo'
    ];

    public function akunKas()
    {
        return $this->belongsTo(Akun::class, 'akun_kas_id');
    }
}
