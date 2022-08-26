<?php

namespace App\Models\Stock;

use App\Haramain\Traits\ModelTraits\{SupplierTraits, UserTraits};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preorder extends Model
{
    use HasFactory, SupplierTraits, UserTraits;

    protected $table = 'stock_preorder';
    protected $fillable = [
        'kode',
        'active_cash',
        'tgl_preorder',
        'tgl_selesai',
        'status',
        'supplier_id',
        'user_id',
        'total_barang',
        'keterangan',
    ];

    public function preorderDetail()
    {
        return $this->hasMany(PreorderDetail::class, 'stock_preorder_id');
    }
}
