<?php

namespace App\Models\Keuangan;

use App\Haramain\Traits\ModelTraits\CustomerTraits;
use App\Haramain\Traits\ModelTraits\KodeTraits;
use App\Haramain\Traits\ModelTraits\UserTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalSetReturPenjualanAwal extends Model
{
    use HasFactory, UserTraits, CustomerTraits, KodeTraits;
    protected $table='haramain_keuangan.jurnal_set_retur_penjualan_awal';
    protected $fillable = [
        'active_cash',
        'kode',
        'tgl_jurnal',
        'customer_id',
        'user_id',
        'total_piutang',
        'keterangan',
    ];

    public function penjualan_piutang_retur()
    {
        return $this->hasMany(PenjualanPiutangRetur::class, 'jurnal_set_retur_penjualan_id');
    }

    public function jurnal_transaksi()
    {
        return $this->morphMany(JurnalTransaksi::class, 'jurnalable_transaksi', 'jurnal_type', 'jurnal_id');
    }
}
