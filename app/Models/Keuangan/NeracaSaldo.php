<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\AkunTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeracaSaldo extends Model
{
    use HasFactory, AkunTrait;

    protected $table = 'haramain_keuangan.neraca_saldo';
    protected $fillable = [
        'active_cash',
        'akun_id',
        'type',
        'debet',
        'kredit',
    ];

    //
    public function scopeGetByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeIncrementByType($query, $type, $nominal)
    {
        return $query->getByType($type)
            ->increment($type. $nominal);
    }

    public function scopeDecrementByType($query, $type, $nominal)
    {
        return $query->getByType($type)
            ->increment($type, $nominal);
    }

}
