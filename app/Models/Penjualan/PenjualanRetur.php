<?php

namespace App\Models\Penjualan;

use App\Haramain\Service\SistemKeuangan\Kasir\PiutangPenjualanTrait;
use App\Models\Keuangan\PersediaanTransaksi;
use App\Models\Keuangan\PiutangPenjualan;
use App\Haramain\Traits\ModelTraits\{CustomerTraits,
    GudangTraits,
    JurnalTransaksiTraits,
    KodeTraits,
    StockMasukTraits,
    UserTraits};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanRetur extends Model
{
    use HasFactory, KodeTraits, CustomerTraits, GudangTraits, UserTraits, StockMasukTraits, JurnalTransaksiTraits;
    protected $table = 'haramainv2.penjualan_retur';
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

    public function tglNota():Attribute
    {
        return Attribute::make(
            get: fn ($value) => tanggalan_format($value),
            set: fn ($value) => tanggalan_database_format($value, 'd-M-Y')
        );
    }

    public function returDetail()
    {
        return $this->hasMany(PenjualanReturDetail::class, 'penjualan_retur_id');
    }

    public function persediaan_transaksi()
    {
        return $this->morphOne('persediaanable_transaksi', PersediaanTransaksi::class, 'persediaan_type', 'persediaan_id');
    }

    public function piutangPenjualan()
    {
        return $this->morphOne(PiutangPenjualan::class, 'piutangablePenjualan', 'penjualan_type', 'penjualan_id');
    }
}
