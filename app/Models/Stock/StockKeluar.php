<?php

namespace App\Models\Stock;

use App\Models\Keuangan\JurnalHPP;
use App\Haramain\Traits\ModelTraits\{GudangTraits, HPPTrait, KodeTraits, SupplierTraits, UserTraits};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockKeluar extends Model
{
    use HasFactory, KodeTraits, GudangTraits, UserTraits, SupplierTraits, HPPTrait;
    protected $table = 'stock_keluar';
    protected $fillable = [
        'kode',
        'supplier_id',
        'active_cash',
        'stockable_keluar_id',
        'stockable_keluar_type',
        'kondisi',
        'gudang_id',
        'tgl_keluar',
        'user_id',
        'keterangan',
    ];

    public function scopeActive($query, $session)
    {
        return $query->where('active_cash', $session);
    }

    public function stockKeluarDetail()
    {
        return $this->hasMany(StockKeluarDetail::class, 'stock_keluar_id');
    }

    // morph to
    public function stockable_keluar()
    {
        return $this->morphTo(__FUNCTION__, 'stockable_keluar_type', 'stockable_keluar_id');
    }

}
