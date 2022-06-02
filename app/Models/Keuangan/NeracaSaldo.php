<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\AkunTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeracaSaldo extends Model
{
    use HasFactory, AkunTrait;
    // protected $connection = 'mysql2';
    protected $table = 'haramain_keuangan.neraca_saldo';
    protected $fillable = [
        'active_cash',
        'akun_id',
        'debet',
        'kredit',
    ];

}
