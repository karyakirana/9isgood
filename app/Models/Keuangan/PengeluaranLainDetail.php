<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranLainDetail extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.pengeluaran_lain_detail';
    protected $fillable = [
        'pengeluaran_lain_id',
        'akun_id',
        'nominal'
    ];

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id');
    }

    public function pengeluaranLain()
    {
        return $this->belongsTo(PengeluaranLain::class, 'pengeluaran_lain_id');
    }
}
