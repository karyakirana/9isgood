<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\PenjualanTraits;
use App\Models\Master\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PiutangPenjualan extends Model
{
    use HasFactory, PenjualanTraits;
    protected $connection = 'mysql2';
    protected $table = 'haramain_keuangan.piutang_penjualan';

    protected $fillable = [
        'saldo_piutang_penjualan_id',
        'jurnal_set_piutang_awal_id',
        'penjualan_type',
        'penjualan_id',
        'status_bayar', // enum ['lunas', 'belum', 'kurang']
        'kurang_bayar',
    ];

    public function saldo_piutang_penjualan(): BelongsTo
    {
        return $this->belongsTo(SaldoPiutangPenjualan::class, 'saldo_piutang_penjualan_id', 'customer_id');
    }

    public function jurnal_set_piutang_awal(): BelongsTo
    {
        return $this->belongsTo(JurnalSetPiutangAwal::class, 'jurnal_set_piutang_awal_id');
    }

    public function penerimaanPenjualanDetail(): MorphMany
    {
        return $this->morphMany(PenerimaanPenjualanDetail::class, 'piutangPenjualan', 'piutang_penjualan_type', 'piutang_penjualan_id');
    }

    public function piutangablePenjualan(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'penjualan_type', 'penjualan_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'saldo_piutang_penjualan_id');
    }
}
