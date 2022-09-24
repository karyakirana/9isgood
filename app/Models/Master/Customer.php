<?php

namespace App\Models\Master;

use App\Haramain\Traits\ModelTraits\KodeTraits;
use App\Models\Keuangan\SaldoPiutangPenjualan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, KodeTraits;

    protected $table = 'haramainv2.customer';
    protected $fillable = [
        'kode',
        'nama',
        'diskon',
        'telepon',
        'alamat',
        'keterangan',
    ];

    public function saldoPiutangPenjualan()
    {
        return $this->hasOne(SaldoPiutangPenjualan::class, 'customer_id');
    }
}
