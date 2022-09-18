<?php

namespace App\Models\Keuangan;

use App\Haramain\Service\SistemKeuangan\Jurnal\KasModelTrait;
use App\Haramain\Traits\ModelTraits\CustomerTraits;
use App\Haramain\Traits\ModelTraits\KodeTraits;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenerimaanPenjualan extends Model
{
    use HasFactory, CustomerTraits, KasModelTrait, KodeTraits;
    protected $table = 'haramain_keuangan.penerimaan_penjualan';
    protected $fillable = [
        'active_cash',
        'kode',
        'tgl_penerimaan',
        'customer_id',
        'akun_kas_id',
        'nominal_kas',
        'akun_piutang_id',
        'nominal_piutang',
    ];

    public function tglPenerimaan():Attribute
    {
        return Attribute::make(
            get: fn($value) => tanggalan_format($value),
            set: fn($value) => tanggalan_database_format($value, 'd-M-Y')
        );
    }

    public function penerimaanPenjualanDetail(): HasMany
    {
        return $this->hasMany(PenerimaanPenjualanDetail::class, 'penerimaan_penjualan_id');
    }

    public function akunKas(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'akun_kas_id');
    }

    public function akun_piutang_id(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'akun_piutang_id');
    }
}
