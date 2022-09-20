<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\GudangTraits;
use App\Haramain\Traits\ModelTraits\ProdukTraits;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersediaanOpnamePrice extends Model
{
    use HasFactory, GudangTraits, ProdukTraits;
    protected $table = 'haramain_keuangan.persediaan_stock_opname_price';
    protected $fillable = [
        'active_cash',
        'tgl_input',
        'kondisi',
        'gudang_id',
        'produk_id',
        'harga'
    ];

    public function tglInput():Attribute
    {
        return Attribute::make(
            get: fn($value) => tanggalan_format($value),
            set: fn($value) => tanggalan_database_format($value, 'd-M-Y')
        );
    }
}
