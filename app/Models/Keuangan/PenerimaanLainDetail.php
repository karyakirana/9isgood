<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanLainDetail extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.penerimaan_lain_detail';
    protected $fillable = [
        'penerimaan_lain_id',
        'akun_id',
        'nominal'
    ];

    public function penerimaanLain()
    {
        return $this->belongsTo(PenerimaanLain::class, 'penerimaan_lain_id');
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id');
    }
}
