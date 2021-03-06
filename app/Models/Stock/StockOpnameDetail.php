<?php

namespace App\Models\Stock;

use App\Haramain\Traits\ModelTraits\{ProdukTraits};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameDetail extends Model
{
    use HasFactory, ProdukTraits;

    protected $table = 'stock_opname_detail';
    protected $fillable = [
        'stock_opname_id',
        'produk_id',
        'jumlah',
    ];

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }
}
