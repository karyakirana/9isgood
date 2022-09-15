<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\SupplierTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoHutangPembelian extends Model
{
    use HasFactory, SupplierTraits;

    protected $table = 'haramain_keuangan.saldo_hutang_pembelian';
    protected $primaryKey = 'supplier_id';
    public $incrementing = false;
    protected $fillable = [
        'supplier_id', 'saldo'
    ];

    public function hutang_pembelian()
    {
        return $this->hasMany(HutangPembelian::class, 'saldo_hutang_pembelian_id', 'supplier_id');
    }
}
