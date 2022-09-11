<?php

namespace App\Http\Livewire\Testing;

use App\Models\Master\Gudang;
use App\Models\Penjualan\PenjualanDetail;
use App\Models\Penjualan\PenjualanReturDetail;
use App\Models\Purchase\Pembelian;
use App\Models\Purchase\PembelianDetail;
use App\Models\Stock\StockMutasi;
use App\Models\Stock\StockMutasiDetail;
use App\Models\Stock\StockOpname;
use App\Models\Stock\StockOpnameDetail;
use App\Models\Stock\StockOpnameKoreksiDetail;
use Livewire\Component;

class StockCardTestTable extends Component
{
    public $produk_id, $queryData = [];
    public $gudang_id, $gudang;

    public function mount($produk_id, $gudang_id=null)
    {
        $this->produk_id = $produk_id;
        if ($gudang_id){
            $this->gudang = Gudang::query()->find($gudang_id)->nama;
            $this->gudang_id = $gudang_id;
            $this->queryData = $this->queryMeGudang();
        } else {
            //$this->queryData = $this->queryMe();
        }
    }

    protected function queryMeGudang()
    {
        $stockOpname = StockOpnameDetail::query()
            ->select([
                'tgl_input as tanggal',
                'stock_opname.kode as kode',
                'produk.nama as nama',
                'pegawai.nama as nama_keterangan'
            ])
            ->selectRaw('stock_opname_detail.jumlah as jumlah_masuk, NULL as jumlah_keluar')
            ->join('stock_opname', 'stock_opname.id', '=', 'stock_opname_detail.stock_opname_id')
            ->join('produk', 'produk.id', '=', 'stock_opname_detail.produk_id')
            ->join('pegawai', 'pegawai.id', '=', 'stock_opname.pegawai_id')
            ->where('stock_opname.active_cash', session('ClosedCash'))
            ->where('stock_opname.jenis', 'baik')
            ->where('stock_opname.gudang_id', $this->gudang_id)
            ->where('stock_opname_detail.produk_id', $this->produk_id);

        $stockOpnameRevisiTambah = StockOpnameKoreksiDetail::query()
            ->select([
                'tgl_input as tanggal',
                'stock_opname_koreksi.kode as kode',
                'produk.nama as nama',
            ])
            ->selectRaw('NULL as nama_keterangan, stock_opname_koreksi_detail.jumlah as jumlah_masuk, NULL as jumlah_keluar')
            ->join('stock_opname_koreksi', 'stock_opname_koreksi.id', '=', 'stock_opname_koreksi_detail.stock_opname_koreksi_id')
            ->join('produk', 'produk.id', '=', 'stock_opname_koreksi_detail.produk_id')
            ->where('stock_opname_koreksi.active_cash', session('ClosedCash'))
            ->where('stock_opname_koreksi.jenis', 'tambah')
            ->where('stock_opname_koreksi.kondisi', 'baik')
            ->where('stock_opname_koreksi.gudang_id', $this->gudang_id)
            ->where('stock_opname_koreksi_detail.produk_id', $this->produk_id);

        $stockOpnameRevisiKurang = StockOpnameKoreksiDetail::query()
            ->select([
                'tgl_input as tanggal',
                'stock_opname_koreksi.kode as kode',
                'produk.nama as nama',
            ])
            ->selectRaw('NULL as nama_keterangan, NULL as jumlah_masuk, stock_opname_koreksi_detail.jumlah as jumlah_keluar')
            ->join('stock_opname_koreksi', 'stock_opname_koreksi.id', '=', 'stock_opname_koreksi_detail.stock_opname_koreksi_id')
            ->join('produk', 'produk.id', '=', 'stock_opname_koreksi_detail.produk_id')
            ->where('stock_opname_koreksi.active_cash', session('ClosedCash'))
            ->where('stock_opname_koreksi.jenis', 'kurang')
            ->where('stock_opname_koreksi.kondisi', 'baik')
            ->where('stock_opname_koreksi.gudang_id', $this->gudang_id)
            ->where('stock_opname_koreksi_detail.produk_id', $this->produk_id);

        $mutasiKeluar = StockMutasiDetail::query()
            ->select([
                'tgl_mutasi as tanggal',
                'stock_mutasi.kode as kode',
                'produk.nama as nama',
            ])
            ->selectRaw('NULL as nama_keterangan, NULL as jumlah_keluar, stock_mutasi_detail.jumlah as jumlah_masuk')
            ->join('stock_mutasi', 'stock_mutasi.id', '=', 'stock_mutasi_detail.stock_mutasi_id')
            ->join('produk', 'produk.id', '=', 'stock_mutasi_detail.produk_id')
            ->where('stock_mutasi.active_cash', session('ClosedCash'))
            ->where('stock_mutasi.jenis_mutasi', 'baik_baik')
            ->where('stock_mutasi.gudang_asal_id', $this->gudang_id)
            ->where('stock_mutasi_detail.produk_id', $this->produk_id);

        $mutasiMasuk = StockMutasiDetail::query()
            ->select([
                'tgl_mutasi as tanggal',
                'stock_mutasi.kode as kode',
                'produk.nama as nama',
            ])
            ->selectRaw('NULL as nama_keterangan, stock_mutasi_detail.jumlah as jumlah_masuk, NULL as jumlah_keluar')
            ->join('stock_mutasi', 'stock_mutasi.id', '=', 'stock_mutasi_detail.stock_mutasi_id')
            ->join('produk', 'produk.id', '=', 'stock_mutasi_detail.produk_id')
            ->where('stock_mutasi.active_cash', session('ClosedCash'))
            ->where('stock_mutasi.jenis_mutasi', 'baik_baik')
            ->where('stock_mutasi.gudang_tujuan_id', $this->gudang_id)
            ->where('stock_mutasi_detail.produk_id', $this->produk_id);

        $pembelian= PembelianDetail::query()
            ->select([
                'tgl_nota as tanggal',
                'pembelian.kode as kode',
                'produk.nama as nama',
                'supplier.nama as nama_keterangan',
            ])
            ->selectRaw('pembelian_detail.jumlah as jumlah_masuk, NULL as jumlah_keluar')
            ->join('pembelian', 'pembelian.id', '=', 'pembelian_detail.pembelian_id')
            ->join('supplier', 'supplier.id', '=', 'pembelian.supplier_id')
            ->join('produk', 'produk.id', '=', 'pembelian_detail.produk_id')
            ->where('pembelian.active_cash', session('ClosedCash'))
            ->where('pembelian.gudang_id', $this->gudang_id)
            ->where('pembelian_detail.produk_id', $this->produk_id);

        $penjualanReturDetail = PenjualanReturDetail::query()
            ->select([
                'tgl_nota as tanggal',
                'penjualan_retur.kode as kode',
                'produk.nama as nama',
                'customer.nama as nama_keterangan'
            ])
            ->selectRaw('penjualan_retur_detail.jumlah as jumlah_masuk, NULL as jumlah_keluar')
            ->join('penjualan_retur', 'penjualan_retur.id', '=', 'penjualan_retur_detail.penjualan_retur_id')
            ->join('customer', 'customer.id', '=', 'penjualan_retur.customer_id')
            ->join('produk', 'produk.id', '=', 'penjualan_retur_detail.produk_id')
            ->where('penjualan_retur.active_cash', session('ClosedCash'))
            ->where('penjualan_retur.gudang_id', $this->gudang_id)
            ->where('penjualan_retur_detail.produk_id', $this->produk_id);

        $penjualanDetail = PenjualanDetail::query()
            ->select([
                'tgl_nota as tanggal',
                'penjualan.kode as kode',
                'produk.nama as nama',
                'customer.nama as nama_keterangan',
            ])
            ->selectRaw('NULL as jumlah_masuk, penjualan_detail.jumlah as jumlah_keluar')
            ->join('penjualan', 'penjualan.id', '=', 'penjualan_detail.penjualan_id')
            ->join('customer', 'customer.id', '=', 'penjualan.customer_id')
            ->join('produk', 'produk.id', '=', 'penjualan_detail.produk_id')
            ->where('penjualan.active_cash', session('ClosedCash'))
            ->where('penjualan.gudang_id', $this->gudang_id)
            ->where('penjualan_detail.produk_id', $this->produk_id)
            ->unionAll($penjualanReturDetail)
            ->unionAll($pembelian)
            ->unionAll($mutasiMasuk)
            ->unionAll($mutasiKeluar)
            ->unionAll($stockOpname)
            ->unionAll($stockOpnameRevisiKurang)
            ->unionAll($stockOpnameRevisiTambah)
            ->oldest('tanggal')
            ->get();
        return $penjualanDetail;
    }

    public function render()
    {
        return view('livewire.testing.stock-card-test-table');
    }
}
