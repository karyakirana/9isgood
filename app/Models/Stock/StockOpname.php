<?php

namespace App\Models\Stock;

use App\Models\Keuangan\PersediaanOpname;
use App\Haramain\Traits\ModelTraits\{GudangTraits, KodeTraits, PegawaiTraits, UserTraits};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory, KodeTraits, GudangTraits, UserTraits, PegawaiTraits;

    protected $table = 'stock_opname';
    protected $fillable = [
        'active_cash',
        'kode',
        'jenis',
        'tgl_input',
        'gudang_id',
        'user_id',
        'pegawai_id',
        'keterangan',
    ];

    public function scopeActive($query, $session)
    {
        return $query->where('active_cash', $session);
    }

    public function stockOpnameDetail()
    {
        return $this->hasMany(StockOpnameDetail::class, 'stock_opname_id');
    }

    public function persediaanOpname()
    {
        return $this->hasOne(PersediaanOpname::class, 'stock_opname_id');
    }
}
