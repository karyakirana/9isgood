<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\KodeTraits;
use App\Haramain\Traits\ModelTraits\UserTraits;
use App\Models\Master\PersonRelation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanLain extends Model
{
    use HasFactory, KodeTraits, UserTraits;
    protected $table = 'haramain_keuangan.penerimaan_lain';
    protected $fillable = [
        'active_cash',
        'kode',
        'tgl_penerimaan',
        'person_relation_id',
        'asal',
        'user_id',
        'nominal',
        'keterangan'
    ];

    public function tglPenerimaan():Attribute
    {
        return Attribute::make(
            get: fn($value) => tanggalan_format($value),
            set: fn($value) => tanggalan_database_format($value, 'd-M-Y')
        );
    }

    public function penerimaanLainDetail()
    {
        return $this->hasMany(PenerimaanLainDetail::class, 'penerimaan_lain_id');
    }

    public function personRelation()
    {
        return $this->belongsTo(PersonRelation::class, 'person_relation_id');
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
