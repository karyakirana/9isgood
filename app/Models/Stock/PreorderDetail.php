<?php

namespace App\Models\Stock;

use App\Haramain\Traits\ModelTraits\{ProdukTraits};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreorderDetail extends Model
{
    use HasFactory, ProdukTraits;

    protected $table = 'stock_preorder_detail';
    protected $fillable = [
        'stock_preorder_id',
        'produk_id',
        'jumlah',
    ];

    public function preorder()
    {
        return $this->belongsTo(Preorder::class, 'stock_preorder_id');
    }
}
