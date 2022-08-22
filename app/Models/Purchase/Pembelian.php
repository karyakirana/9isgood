<?php

namespace App\Models\Purchase;

use App\Models\Keuangan\HutangPembelian;
use App\Models\Keuangan\PersediaanTransaksi;
use App\Models\Stock\StockMasuk;
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
    protected $table = 'pembelian';
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
        return $this->morphOne(HutangPembelian::class, 'hutang_pembelian_morph', 'pembelian_type', 'pembelian_id');
    }

}
