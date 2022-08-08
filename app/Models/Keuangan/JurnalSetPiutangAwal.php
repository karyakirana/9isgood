<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\CustomerTraits;
use App\Haramain\Traits\ModelTraits\KodeTraits;
use App\Haramain\Traits\ModelTraits\UserTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalSetPiutangAwal extends Model
{
    use HasFactory, KodeTraits, CustomerTraits, UserTraits;
    protected $table = 'haramain_keuangan.jurnal_set_piutang_awal';
    protected $fillable = [
        'active_cash',
        'kode',
        'jenis',
        'tgl_jurnal',
        'customer_id',
        'user_id',
        'total_piutang',
        'keterangan',
    ];

    public function piutang_penjualan()
    {
        return $this->hasMany(PiutangPenjualan::class, 'jurnal_set_piutang_awal_id');
    }

    public function jurnal_transaksi()
    {
        return $this->morphMany(JurnalTransaksi::class, 'jurnalable_transaksi', 'jurnal_type', 'jurnal_id');
    }
}
