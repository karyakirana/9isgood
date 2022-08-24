<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\ProdukTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersediaanTransaksiDetail extends Model
{
    use HasFactory, ProdukTraits;
    protected $table = 'haramain_keuangan.persediaan_transaksi_detail';
    protected $fillable = [
        'persediaan_transaksi_id',
        'persediaan_id',
        'produk_id',
        'harga',
        'jumlah',
        'sub_total',
    ];

    public function persediaan_transaksi()
    {
        return $this->belongsTo(PersediaanTransaksi::class, 'persediaan_transaksi_id');
    }

    public function persediaan()
    {
        return $this->belongsTo(Persediaan::class, 'persediaan_id');
    }
}
