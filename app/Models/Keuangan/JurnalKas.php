<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\KodeTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalKas extends Model
{
    use HasFactory, KodeTraits;
    protected $connection = 'kas';
    protected $table = 'jurnal_kas';
    protected $fillable = [
        'kode',
        'active_cash',
        'type',
        'jurnal_type',
        'jurnal_id',
        'akun_id',
        'nominal_debet',
        'nominal_kredit',
        'nominal_saldo',
    ];

    public function jurnalable_kas()
    {
        return $this->morphTo(__FUNCTION__, 'jurnal_type', 'jurnal_id');
    }
}
