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
        'cash_type',
        'cash_id',
        'akun_id',
        'nominal_debet',
        'nominal_kredit',
    ];

    public function jurnalable_kas()
    {
        return $this->morphTo(__FUNCTION__, 'cash_type', 'cash_id');
    }
}
