<?php

namespace App\Http\Livewire\Test;

use App\Haramain\Service\SistemStock\SubStockMasuk\MencatatStockMasuk;
use Livewire\Component;

class PenjualanForm extends Component
{
    public $penjualan;
    public $penjualan_id;
    public $tgl_nota, $tgl_tempo;
    public $customer_id, $customer_diskon;
    public $customer_nama;
    public $jenis_bayar;
    public $gudang_id;
    public $keterangan;

    public $penjualan_detail;
    public $produk;
    public $produk_id, $produk_nama, $produk_kode_lokal, $produk_harga;
    public $harga_setelah_diskon;
    public $jumlah, $sub_total;

    public $total_barang;
    public $total, $ppn, $biaya_lain, $total_bayar;

    public function render()
    {
        return view('livewire.test.penjualan-form');
    }

    public function store()
    {
        // handle validation
        // penjualan handle service
    }

    public function update()
    {
        // handle validation
        // penjualan handle service
    }
}
