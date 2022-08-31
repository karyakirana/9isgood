<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkunTipe extends Model
{
    use HasFactory;
    protected $table = 'akun_tipe';
    protected $fillable = [
        'kode',
        'default_saldo',
        'deskripsi',
        'keterangan',
    ];

    public function akun()
    {
        return $this->hasMany(Akun::class, 'akun_tipe_id');
    }
}
