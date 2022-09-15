<?php

namespace App\Models\Stock;

use App\Models\Keuangan\JurnalPersediaanMutasi;
use App\Models\Keuangan\PersediaanMutasi;
use App\Haramain\Traits\ModelTraits\{GudangTraits, KodeTraits, StockKeluarTraits, StockMasukTraits, UserTraits};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMutasi extends Model
{
    use HasFactory, KodeTraits, GudangTraits, UserTraits;
    use StockMasukTraits, StockKeluarTraits;

    protected $table = 'haramainv2.stock_mutasi';
    protected $fillable = [
        'active_cash',
        'kode',
        'jenis_mutasi',
        'gudang_asal_id',
        'gudang_tujuan_id',
        'tgl_mutasi',
        'user_id',
        'keterangan',
    ];

    public function tglMutasi():Attribute
    {
        return Attribute::make(
            get : fn ($value) => tanggalan_format($value),
            set : fn ($value) => tanggalan_database_format($value, 'd-M-Y')
        );
    }

    public function stockMutasiDetail()
    {
        return $this->hasMany(StockMutasiDetail::class, 'stock_mutasi_id');
    }

    public function jurnalPersediaanTransaksi()
    {
        return $this->hasMany(JurnalPersediaanMutasi::class, 'stock_mutasi_id');
    }

    public function persediaanMutasi()
    {
        return $this->hasOne(PersediaanMutasi::class, 'stock_mutasi_id');
    }
}
