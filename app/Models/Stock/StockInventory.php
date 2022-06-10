<?php

namespace App\Models\Stock;

use App\Haramain\Service\SystemCore\SessionScope;
use App\Haramain\Traits\ModelTraits\{GudangTraits, ProdukTraits};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInventory extends Model
{
    use HasFactory, GudangTraits, ProdukTraits;

    protected $table = 'stock_inventory';
    protected $fillable = [
        'active_cash',
        'jenis',
        'gudang_id',
        'produk_id',
        'stock_awal',
        'stock_opname',
        'stock_masuk',
        'stock_keluar',
        'stock_saldo',
        'stock_akhir',
        'stock_lost',
    ];

    public function scopeSessionActive($query)
    {
        $query->where('active_cash', session('ClosedCash'));
    }

    public function scopeByKondisi($query, $kondisi)
    {
        $query->where('jenis', $kondisi);
    }
}
