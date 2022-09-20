<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\GudangTraits;
use App\Models\Stock\StockOpnameKoreksi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersediaanOpnameKoreksi extends Model
{
    use HasFactory, GudangTraits;
    protected $table = 'haramain_keuangan.persediaan_opname_koreksi';
    protected $fillable = [
        'active_cash',
        'stock_opname_koreksi_id',
        'jenis', // tambah or kurang
        'kondisi',
        'gudang_id',
        'user_id'
    ];

    public function stockOpnameKoreksi()
    {
        return $this->belongsTo(StockOpnameKoreksi::class, 'stock_opname_koreksi_id');
    }

    public function persediaanOpnameKoreksiDetail()
    {
        return $this->hasMany(PersediaanOpnameKoreksiDetail::class, 'persediaan_koreksi_opname_id');
    }
}
