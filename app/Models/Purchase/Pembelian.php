<?php

namespace App\Models\Purchase;

use App\Models\Keuangan\HutangPembelian;
use App\Models\Keuangan\PersediaanTransaksi;
use App\Models\Stock\StockMasuk;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Haramain\Traits\ModelTraits\{JurnalTransaksiTraits,
    KodeTraits,
    SupplierTraits,
    GudangTraits,
    UserTraits,
    StockMasukTraits};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory, KodeTraits, SupplierTraits, GudangTraits, UserTraits, StockMasukTraits, JurnalTransaksiTraits;
    protected $table = 'haramainv2.pembelian';
    protected $fillable = [
        'kode',
        'nomor_nota',
        'nomor_surat_jalan',
        'jenis',
        'active_cash',
        'supplier_id',
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

    public function tglNota(): Attribute
    {
        return Attribute::make(
            get: fn($value) => tanggalan_format($value),
            set: fn($value) => tanggalan_database_format($value, 'd-M-Y')
        );
    }

    public function pembelianDetail()
    {
        return $this->hasMany(PembelianDetail::class, 'pembelian_id');
    }

    public function persediaan_transaksi()
    {
        return $this->morphOne(PersediaanTransaksi::class, 'persediaanable_transaksi', 'persediaan_type', 'persediaan_id');
    }

    public function hutang_pembelian()
    {
        return $this->morphOne(HutangPembelian::class, 'hutangablePembelian', 'pembelian_type', 'pembelian_id');
    }

}
