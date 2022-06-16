<?php

namespace App\Models\Stock;

use App\Haramain\Traits\ModelTraits\{GudangTraits, KodeTraits, SupplierTraits, UserTraits};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMasuk extends Model
{
    use HasFactory, KodeTraits, SupplierTraits, GudangTraits, UserTraits;

    protected $table = 'stock_masuk';
    protected $fillable = [
        'kode',
        'active_cash',
        'stockable_masuk_id',
        'stockable_masuk_type',
        'kondisi',
        'gudang_id',
        'supplier_id',
        'tgl_masuk',
        'user_id',
        'nomor_po',
        'nomor_surat_jalan',
        'keterangan',
    ];

    public function scopeActive($query, $session)
    {
        return $query->where('active_cash', $session);
    }

    public function stockable_masuk()
    {
        return $this->morphTo(__FUNCTION__, 'stockable_masuk_type', 'stockable_masuk_id');
    }

    public function stockMasukDetail()
    {
        return $this->hasMany(StockMasukDetail::class, 'stock_masuk_id');
    }
}
