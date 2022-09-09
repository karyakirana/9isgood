<?php

namespace App\Models\Stock;

use App\Haramain\Traits\ModelTraits\GudangTraits;
use App\Haramain\Traits\ModelTraits\KodeTraits;
use App\Haramain\Traits\ModelTraits\UserTraits;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameKoreksi extends Model
{
    use HasFactory;
    use GudangTraits, UserTraits, KodeTraits;
    protected $table = 'haramainv2.stock_opname_koreksi';
    protected $fillable = [
        'active_cash',
        'kode',
        'jenis', // tambah atau kurang
        'kondisi',
        'tgl_input',
        'gudang_id',
        'user_id',
        'keterangan',
    ];

    // mutator
    public function tglInput():Attribute
    {
        return Attribute::make(
            get: fn($value) => tanggalan_format($value),
            set: fn($value) => tanggalan_database_format($value, 'd-M-Y')
        );
    }

    public function stockOpnameKoreksiDetail()
    {
        return $this->hasMany(StockOpnameKoreksiDetail::class, 'stock_opname_koreksi_id');
    }
}
