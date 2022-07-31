<?php

namespace App\Models\Penjualan;

use App\Haramain\Service\SistemKeuangan\Kasir\PiutangPenjualanTrait;
use App\Haramain\Traits\ModelTraits\{CustomerTraits,
    GudangTraits,
    JurnalTransaksiTraits,
    KodeTraits,
    StockMasukTraits,
    UserTraits};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanRetur extends Model
{
    use HasFactory, KodeTraits, CustomerTraits, GudangTraits, UserTraits, StockMasukTraits, JurnalTransaksiTraits;
    use PiutangPenjualanTrait;
    protected $table = 'penjualan_retur';
    protected $fillable = [
        'kode',
        'active_cash',
        'jenis_retur',
        'customer_id',
        'gudang_id',
        'user_id',
        'tgl_nota',
        'tgl_tempo',
        'status_bayar',
        'total_barang',
        'ppn',
        'biaya_lain',
        'total_bayar',
        'keterangan',
    ];

    public function returDetail()
    {
        return $this->hasMany(PenjualanReturDetail::class, 'penjualan_retur_id');
    }
}
