<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\KodeTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalKas extends Model
{
    use HasFactory, KodeTraits;
    protected $table = 'haramain_keuangan.kas';
    protected $fillable = [
        'kode',
        'active_cash',
        'type', // debet or kredit (enum)
        'jurnal_type',
        'jurnal_id',
        'akun_id',
        'nominal_debet',
        'nominal_kredit',
    ];

    public function jurnalable_kas()
    {
        return $this->morphTo(__FUNCTION__, 'jurnal_type', 'jurnal_id');
    }
}
