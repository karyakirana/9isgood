<?php

namespace App\Models\Keuangan;

use App\Models\Master\Gudang;
use App\Models\Stock\StockMutasi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersediaanMutasi extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.persediaan_mutasi';
    protected $fillable = [
        'stock_mutasi_id',
        'jenis_mutasi',
        'gudang_asal_id',
        'gudang_tujuan_id',
        'total_barang',
        'total_harga',
    ];

    public function persediaanMutasiDetail()
    {
        return $this->hasMany(PersediaanMutasiDetail::class, 'persediaan_mutasi_id');
    }

    public function stockMutasi()
    {
        return $this->belongsTo(StockMutasi::class, 'stock_mutasi_id');
    }

    public function gudangAsal()
    {
        return $this->belongsTo(Gudang::class, 'gudang_asal_id');
    }

    public function gudangTujuan()
    {
        return $this->belongsTo(Gudang::class, 'gudang_tujuan_id');
    }

    public function persediaan_transaksi()
    {
        return $this->morphMany(PersediaanTransaksi::class, 'persediaanable_transaksi', 'persediaan_type', 'persediaan_id');
    }
}
