<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\GudangTraits;
use App\Haramain\Traits\ModelTraits\KodeTraits;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersediaanTransaksi extends Model
{
    use HasFactory, KodeTraits, GudangTraits;

//    protected $connection = 'mysql2';
    protected $table = 'haramain_keuangan.persediaan_transaksi';
    protected $fillable = [
        'active_cash',
        'kode',
        'jenis', // masuk atau keluar
        'tgl_input',
        'kondisi', // baik atau rusak
        'gudang_id',
        'persediaan_type',
        'persediaan_id',
        'debet',
        'kredit',
    ];

    public function tglInput():Attribute
    {
        return Attribute::make(
            get: fn($value) => tanggalan_format($value),
            set: fn($value) => tanggalan_database_format($value, 'd-M-Y')
        );
    }

    public function persediaanable_transaksi()
    {
        return $this->morphTo(__FUNCTION__, 'persediaan_type', 'persediaan_id');
    }

    public function persediaan_transaksi_detail()
    {
        return $this->hasMany(PersediaanTransaksiDetail::class, 'persediaan_transaksi_id');
    }

    public function persediaan()
    {
        return $this->hasMany(Persediaan::class, 'persediaan_transaksi_id');
    }

    public function persediaanOut()
    {
        return $this->hasMany(PersediaanOut::class, 'persediaan_transaksi_id');
    }

    public function jurnal_transaksi()
    {
        return $this->morphMany(JurnalTransaksi::class, 'jurnalable_transaksi', 'jurnal_type', 'jurnal_id');
    }
}
