<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\CustomerTraits;
use App\Haramain\Traits\ModelTraits\UserTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PiutangPenjualanLama extends Model
{
    use HasFactory, CustomerTraits, UserTraits;
    protected $table = 'haramain_keuangan.piutang_penjualan_lama';
    protected $fillable = [
        'tahun_nota',
        'customer_id',
        'user_Id',
        'total_piutang',
        'keterangan',
    ];

    public function piutangPenjualanLamaDetail(): HasMany
    {
        return $this->hasMany(PiutangPenjualanLamaDetail::class, 'piutang_penjualan_lama_id');
    }

    public function jurnalTransaksi(): MorphMany
    {
        return $this->morphMany(JurnalTransaksi::class, 'jurnalable_transaksi', 'jurnal_type', 'jurnal_id');
    }

    public function penerimaanPenjualanDetail(): MorphMany
    {
        return $this->morphMany(PenerimaanPenjualanDetail::class, 'piutangPenjualan', 'piutang_penjualan_type', 'piutang_penjualan_id');
    }
}
