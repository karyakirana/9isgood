<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\ProdukTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersediaanOpnameKoreksiDetail extends Model
{
    use HasFactory, ProdukTraits;
    protected $table = 'haramain_keuangan.persediaan_opname_koreksi_detail';
    protected $fillable = [
        'persediaan_koreksi_opname_id',
        'persediaan_id',
        'produk_id',
        'harga',
        'jumlah',
        'sub_total'
    ];

    public function persediaanKoreksiOpname()
    {
        return $this->belongsTo(PersediaanOpnameKoreksi::class, 'persediaan_koreksi_opname_id');
    }

    public function persediaan()
    {
        return $this->belongsTo(Persediaan::class, 'persediaan_id');
    }
}
