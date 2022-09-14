<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasMutasi extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.kas_mutasi';
    protected $fillable = [
        'kode',
        'user_id',
        'total_mutasi',
        'keterangan'
    ];

    public function kasMutasiDetail()
    {
        return $this->hasMany(KasMutasiDetail::class, 'kas_mutasi_id');
    }
}
