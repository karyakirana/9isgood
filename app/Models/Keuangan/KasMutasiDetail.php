<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasMutasiDetail extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.kas_mutasi_detail';
    protected $fillable = [
        'kas_mutasi_id',
        'jenis', // masuk atau keluar
        'akun_kas_id',
        'nominal_masuk',
        'nominal_keluar',
    ];

    public function kasMutasi()
    {
        return $this->belongsTo(KasMutasi::class, 'kas_mutasi_id');
    }

    public function akunKas()
    {
        return $this->belongsTo(Akun::class, 'akun_kas_id');
    }
}
