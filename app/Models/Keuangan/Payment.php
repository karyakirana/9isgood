<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.payment';
    protected $fillable = [
        'paymentable_type',
        'paymentable_id',
        'akun_id',
        'nominal'
    ];

    public function paymentable()
    {
        return $this->morphTo(__FUNCTION__, 'paymentable_type', 'paymentable_id');
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id');
    }
}
