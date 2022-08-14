<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\GudangTraits;
use App\Haramain\Traits\ModelTraits\ProdukTraits;
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
        'stock_saldo',
        'stock_akhir',
        'stock_lost',
    ];
}
