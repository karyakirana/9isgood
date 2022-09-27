<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\KodeTraits;
use App\Haramain\Traits\ModelTraits\UserTraits;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PiutangInternal extends Model
{
    use HasFactory, UserTraits, KodeTraits;
    protected $table = 'haramain_keuangan.piutang_internal';
    protected $fillable = [
        'active_cash',
        'kode',
        'saldo_pegawai_id',
        'jenis_piutang', // penerimaan or pengeluaran
        'tgl_transaksi',
        'user_id',
        'nominal',
        'keterangan'
    ];

    public function tglTransaksi():Attribute
    {
        return Attribute::make(
            get: fn($value) => tanggalan_format($value),
            set: fn($value) => tanggalan_database_format($value, 'd-M-Y')
        );
    }

    public function saldoPegawai()
    {
        return $this->belongsTo(SaldoPegawai::class, 'saldo_pegawai_id');
    }

    public function paymentable()
    {
        return $this->morphMany(Payment::class, 'paymentable', 'paymentable_type', 'paymentable_id');
    }

    public function jurnalKas()
    {
        return $this->morphMany(JurnalKas::class, 'jurnalable_kas', 'jurnal_type', 'jurnal_id');
    }
}
