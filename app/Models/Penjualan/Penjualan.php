<?php

namespace App\Models\Penjualan;

use App\Haramain\Service\SistemKeuangan\Kasir\PiutangPenjualanTrait;
use App\Models\Keuangan\PersediaanTransaksi;
use App\Models\Keuangan\PiutangPenjualan;
use App\Models\Keuangan\PiutangPenjualanLama;
use App\Models\Keuangan\PiutangPenjualanLamaDetail;
use App\Haramain\Traits\ModelTraits\{CustomerTraits,
    GudangTraits,
    JurnalTransaksiTraits,
    KodeTraits,
    StockKeluarTraits,
    UserTraits};
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory, KodeTraits, CustomerTraits, GudangTraits, UserTraits, StockKeluarTraits;
    use JurnalTransaksiTraits;
    protected $table = 'haramainv2.penjualan';
    protected $fillable = [
        'kode',
        'active_cash',
        'customer_id',
        'gudang_id',
        'user_id',
        'tgl_nota',
        'tgl_tempo',
        'jenis_bayar',
        'status_bayar',
        'total_barang',
        'ppn',
        'biaya_lain',
        'total_bayar',
        'keterangan',
        'print',
    ];

    public function tglNota():Attribute
    {
        return Attribute::make(
            get: fn ($value) => tanggalan_format($value),
            set: fn ($value) => tanggalan_database_format($value, 'd-M-Y'),
        );
    }

    public function tglTempo():Attribute
    {
        return Attribute::make(
            get: fn ($value) => ($value) ? tanggalan_format($value) : null,
            set: fn ($value) => ($value) ? tanggalan_database_format($value, 'd-M-Y') : null,
        );
    }

    public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetail::class, 'penjualan_id');
    }

    public function persediaan_transaksi()
    {
        return $this->morphOne(PersediaanTransaksi::class, 'persediaanable_transaksi', 'persediaan_type', 'persediaan_id');
    }

    public function piutangPenjualanLamaDetail()
    {
        return $this->hasOne(PiutangPenjualanLamaDetail::class, 'penjualan_id');
    }

    public function piutangPenjualan()
    {
        return $this->morphOne(PiutangPenjualan::class, 'piutangablePenjualan', 'penjualan_type', 'penjualan_id');
    }
}
