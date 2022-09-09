<?php

namespace App\Models\Stock;

use App\Haramain\Traits\ModelTraits\ProdukTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameKoreksiDetail extends Model
{
    use HasFactory, ProdukTraits;
    protected $table = 'haramainv2.stock_opname_koreksi_detail';
    protected $fillable = [
        'stock_opname_koreksi_id',
        'produk_id',
        'jumlah',
    ];
}
