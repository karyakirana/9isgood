<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\UserTraits;
use App\Models\Master\PersonRelation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranLain extends Model
{
    use HasFactory, UserTraits;
    protected $table = 'haramain_keuangan.pengeluaran_lain';
    protected $fillable = [
        'active_cash',
        'kode',
        'tgl_pengeluaran',
        'person_relation_id',
        'tujuan',
        'user_id',
        'nominal',
        'keterangan'
    ];

    public function tglPengeluaran():Attribute
    {
        return Attribute::make(
            get: fn($value) => tanggalan_format($value),
            set: fn($value) => tanggalan_database_format($value, 'd-M-Y')
        );
    }

    public function personRelation()
    {
        return $this->belongsTo(PersonRelation::class, 'person_relation_id');
    }

    public function pengeluaranLainDetail()
    {
        return $this->hasMany(PengeluaranLainDetail::class, 'pengeluaran_lain_id');
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
