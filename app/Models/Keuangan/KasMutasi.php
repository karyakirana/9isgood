<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\JurnalTransaksiTraits;
use App\Haramain\Traits\ModelTraits\KodeTraits;
use App\Haramain\Traits\ModelTraits\UserTraits;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasMutasi extends Model
{
    use HasFactory, KodeTraits, UserTraits, JurnalTransaksiTraits;
    protected $table = 'haramain_keuangan.kas_mutasi';
    protected $fillable = [
        'active_cash',
        'kode',
        'tgl_mutasi',
        'user_id',
        'total_mutasi',
        'keterangan'
    ];

    public function tglMutasi():Attribute
    {
        return Attribute::make(
            get: fn ($value) => tanggalan_format($value),
            set: fn ($value) => tanggalan_database_format($value, 'd-M-Y')
        );
    }

    public function jurnalKas()
    {
        return $this->morphMany(JurnalKas::class, 'jurnalable_kas', 'jurnal_type', 'jurnal_id');
    }

    public function kasMutasiDetail()
    {
        return $this->hasMany(KasMutasiDetail::class, 'kas_mutasi_id');
    }
}
