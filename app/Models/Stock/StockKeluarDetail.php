<?php

namespace App\Models\Stock;

use App\Haramain\Traits\ModelTraits\ProdukTraits;
use App\Haramain\Traits\ModelTraits\StockKeluarTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockKeluarDetail extends Model
{
    use HasFactory, StockKeluarTraits, ProdukTraits;

    protected $table = 'haramainv2.stock_keluar_detail';
    protected $fillable = [
        'stock_keluar_id',
        'produk_id',
        'jumlah',
    ];
}
