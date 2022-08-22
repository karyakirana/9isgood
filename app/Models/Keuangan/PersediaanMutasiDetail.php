<?php

namespace App\Models\Keuangan;

use App\Models\Master\Produk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersediaanMutasiDetail extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.persediaan_mutasi_detail';
    protected $fillable = [
        'persediaan_mutasi_id',
        'produk_id',
        'harga',
        'jumlah',
        'sub_total'
    ];

    public function persediaanMutasi()
    {
        return $this->belongsTo(PersediaanMutasi::class, 'persediaan_mutasi_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
