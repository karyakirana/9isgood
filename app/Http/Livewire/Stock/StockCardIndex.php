<?php

namespace App\Http\Livewire\Stock;

use App\Models\Master\Gudang;
use App\Models\Penjualan\Penjualan;
use App\Models\Penjualan\PenjualanRetur;
use App\Models\Purchase\Pembelian;
use Livewire\Component;

class StockCardIndex extends Component
{
    public $produk_id, $queryData;
    public $gudang_id, $gudang;

    public function mount($produk_id, $gudang_id=null)
    {
        $this->produk_id = $produk_id;
        if ($gudang_id){
            $this->gudang = Gudang::query()->find($gudang_id)->nama;
            $this->gudang_id = $gudang_id;
            $this->queryData = $this->queryMeGudang();
        } else {
            $this->queryData = $this->queryMe();
        }
    }

    public function queryMeGudang()
    {
        return \DB::select(\DB::raw(
            "SELECT produk.nama AS nama, penjualan.kode, stock_keluar_detail.produk_id, jumlah AS jumlah_keluar,
                        NULL AS jumlah_masuk, tgl_nota AS tanggal, customer.nama as nama_keterangan
                    from penjualan
                    right join customer
                        on penjualan.customer_id = customer.id
                    right join stock_keluar
                        on penjualan.id = stock_keluar.stockable_keluar_id
                    right join stock_keluar_detail
                        on stock_keluar_detail.stock_keluar_id = stock_keluar.id AND stock_keluar.stockable_keluar_type like '%Penjualan'
                    right join produk
                        on stock_keluar_detail.produk_id = produk.id
                    WHERE produk_id = '".$this->produk_id."'
                        AND penjualan.active_cash = '".session('ClosedCash')."'
                        AND penjualan.gudang_id = '".$this->gudang_id."'

                    UNION ALL

                    select produk.nama as nama, stock_mutasi.kode, stock_keluar_detail.produk_id, jumlah as jumlah_keluar,
                        null as jumlah_masuk, tgl_mutasi as tanggal, null as nama_keterangan
                    from stock_mutasi
                    right join stock_keluar
                        on stock_mutasi.id = stock_keluar.stockable_keluar_id
                    right join stock_keluar_detail
                        on stock_keluar_detail.stock_keluar_id = stock_keluar.id AND stock_keluar.stockable_keluar_type like '%StockMutasi'
                    right join produk
                        on stock_keluar_detail.produk_id = produk.id
                    WHERE produk_id = '".$this->produk_id."'
                        AND stock_mutasi.active_cash = '".session('ClosedCash')."'
                        AND stock_mutasi.gudang_asal_id = '".$this->gudang_id."'
                        AND stock_mutasi.jenis_mutasi = 'baik_baik'
                        OR stock_mutasi.jenis_mutasi = 'baik_rusak'

                    UNION ALL

                    select produk.nama as nama, stock_mutasi.kode, stock_masuk_detail.produk_id, null as jumlah_keluar,
                        jumlah as jumlah_masuk, tgl_mutasi as tanggal, null as nama_keterangan
                    from stock_mutasi
                    right join stock_masuk
                        on (stock_mutasi.id = stock_masuk.stockable_masuk_id AND stock_masuk.stockable_masuk_type like '%StockMutasi')
                    right join stock_masuk_detail
                        on stock_masuk.id = stock_masuk_detail.stock_masuk_id
                    right join produk
                        on stock_masuk_detail.produk_id = produk.id
                    WHERE produk_id = '".$this->produk_id."'
                        AND stock_mutasi.active_cash = '".session('ClosedCash')."'
                        AND stock_mutasi.gudang_tujuan_id = '".$this->gudang_id."'
                        AND stock_mutasi.jenis_mutasi = 'baik_baik'

                    UNION ALL

                    select produk.nama as nama, penjualan_retur.kode, stock_masuk_detail.produk_id, null as jumlah_keluar,
                        jumlah as jumlah_masuk, tgl_nota as tanggal, customer.nama as nama_keterangan
                    from penjualan_retur
                    right join customer
                        on penjualan_retur.customer_id = customer.id
                    right join stock_masuk
                        on (penjualan_retur.id = stock_masuk.stockable_masuk_id AND stock_masuk.stockable_masuk_type like '%PenjualanRetur')
                    right join stock_masuk_detail
                        on stock_masuk.id = stock_masuk_detail.stock_masuk_id
                    right join produk
                        on stock_masuk_detail.produk_id = produk.id
                    WHERE produk_id = '".$this->produk_id."'
                        AND penjualan_retur.active_cash = '".session('ClosedCash')."'
                        AND penjualan_retur.gudang_id = '".$this->gudang_id."'
                        AND penjualan_retur.jenis_retur = 'baik'


                    UNION ALL

                    select produk.nama as nama, pembelian.kode, stock_masuk_detail.produk_id, null as jumlah_keluar,
                        jumlah as jumlah_masuk, tgl_nota as tanggal, supplier.nama as nama_keterangan
                    from pembelian
                    right join supplier
                        on pembelian.supplier_id = supplier.id
                    right join stock_masuk
                        on (pembelian.id = stock_masuk.stockable_masuk_id AND stock_masuk.stockable_masuk_type like '%Pembelian')
                    right join stock_masuk_detail
                        on stock_masuk.id = stock_masuk_detail.stock_masuk_id
                    right join produk
                        on stock_masuk_detail.produk_id = produk.id
                    WHERE produk_id = '".$this->produk_id."'
                        AND pembelian.active_cash = '".session('ClosedCash')."'
                        AND pembelian.gudang_id = '".$this->gudang_id."'

                    UNION ALL

                    select produk.nama as nama, pembelian_retur.kode as kode, stock_keluar_detail.produk_id, jumlah as jumlah_keluar,
                        null as jumlah_masuk, tgl_nota as tanggal, supplier.nama as nama_keterangan
                    from pembelian_retur
                    right join supplier
                        on pembelian_retur.supplier_id = supplier.id
                    right join stock_keluar
                        on (pembelian_retur.id = stock_keluar.stockable_keluar_id AND stock_keluar.stockable_keluar_type like '%Pembelianretur')
                    right join stock_keluar_detail
                        on stock_keluar_detail.stock_keluar_id = stock_keluar.id AND stock_keluar.stockable_keluar_type like '%Penjualan'
                    right join produk
                        on stock_keluar_detail.produk_id = produk.id
                    WHERE produk_id = '".$this->produk_id."'
                        AND pembelian_retur.active_cash = '".session('ClosedCash')."'
                        AND pembelian_retur.gudang_id = '".$this->gudang_id."'
                        AND pembelian_retur.kondisi = 'baik'

                    UNION ALL

                    SELECT produk.nama AS nama, stock_opname.kode, produk_id, NULL AS jumlah_keluar, jumlah AS jumlah_masuk,
                        tgl_input AS tanggal, pegawai.nama as nama_keterangan
                    FROM stock_opname
                    right join pegawai
                        on stock_opname.pegawai_id = pegawai.id
                    right join stock_opname_detail
                        on stock_opname_detail.stock_opname_id = stock_opname.id
                    right join produk
                        on stock_opname_detail.produk_id = produk.id
                    WHERE produk_id = '".$this->produk_id."'
                        AND active_cash = '".session('ClosedCash')."'
                        AND gudang_id = '".$this->gudang_id."'
                        AND jenis = 'baik'

                    ORDER BY tanggal ASC "
        ));
    }

    public function queryMe()
    {
        return \DB::select(\DB::raw(
            "SELECT produk.nama AS nama, pj.kode, produk_id, jumlah AS jumlah_keluar, NULL AS jumlah_masuk,tgl_keluar AS tanggal
                    FROM stock_keluar sk
                    RIGHT JOIN stock_keluar_detail skd
                    ON sk.id = skd.stock_keluar_id
                    RIGHT JOIN produk
                    ON produk.id = skd.produk_id
                    RIGHT JOIN penjualan pj
                    ON pj.id = sk.stockable_keluar_id
                    WHERE produk_id = '".$this->produk_id."'
                    AND sk.active_cash = '".session('ClosedCash')."'

                    UNION ALL

                    SELECT produk.nama AS nama, sm.kode, produk_id, NULL AS jumlah_keluar, jumlah AS jumlah_masuk,tgl_masuk AS tanggal
                    FROM stock_masuk sm
                    RIGHT JOIN stock_masuk_detail smd
                    ON sm.id = smd.stock_masuk_id
                    RIGHT JOIN produk
                    ON produk.id = smd.produk_id
                    WHERE produk_id = '".$this->produk_id."'
                    AND active_cash = '".session('ClosedCash')."'


                    UNION ALL

                    SELECT produk.nama AS nama, so.kode, produk_id, NULL AS jumlah_keluar, jumlah AS jumlah_masuk, tgl_input AS tanggal
                    FROM stock_opname so
                    RIGHT JOIN stock_opname_detail sod
                    ON sod.stock_opname_id = so.id
                    RIGHT JOIN produk
                    ON produk.id = sod.produk_id
                    WHERE produk_id = '".$this->produk_id."'
                    AND active_cash = '".session('ClosedCash')."'

                    ORDER BY tanggal ASC "
        ));
    }

    public function render()
    {
        return view('livewire.stock.stock-card-index', [
            //'produk_id'=>$this->produk_id
        ]);
    }
}
