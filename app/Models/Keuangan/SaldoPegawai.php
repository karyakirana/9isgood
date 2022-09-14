<?php

namespace App\Models\Keuangan;

use App\Models\Master\Pegawai;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoPegawai extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.saldo_pegawai';
    protected $fillable = [
        'pegawai_id',
        'saldo'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
