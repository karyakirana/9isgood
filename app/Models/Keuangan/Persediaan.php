<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\GudangTraits;
use App\Haramain\Traits\ModelTraits\ProdukTraits;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persediaan extends Model
{
    use HasFactory, GudangTraits, ProdukTraits;
    protected $table = 'haramain_keuangan.persediaan';
    protected $fillable = [
        'active_cash',
        'jenis',// baik or buruk
        'tgl_input',
        'gudang_id',
        'produk_id',
        'harga',
        'stock_opname',
        'stock_masuk',
        'stock_keluar',
        'saldo',
        'stock_saldo',
    ];

    public function tglInput(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => tanggalan_format($value),
            set: fn ($value) => tanggalan_database_format($value, 'd-M-Y')
        );
    }
}
