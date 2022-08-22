<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersediaanOut extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.persediaan_out';
    protected $fillable =[
        'persediaan_transaksi_id',
        'persediaan_id'
    ];

    public function persediaanTransaksi()
    {
        return $this->belongsTo(PersediaanTransaksi::class, 'persediaan_transaksi_id');
    }

    public function persediaan()
    {
        return $this->belongsTo(Persediaan::class, 'persediaan_id');
    }
}
