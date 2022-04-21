<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (){

    // master keuangan
    Route::get('keuangan/master/akun', \App\Http\Livewire\Keuangan\Master\AkunIndex::class)->name('keuangan.master.akun');
    Route::get('keuangan/master/akuntipe', \App\Http\Livewire\Keuangan\Master\AkunTipeIndex::class)->name('keuangan.master.akuntipe');
    Route::get('keuangan/master/akunkategori', \App\Http\Livewire\Keuangan\Master\AkunKategoriIndex::class)->name('keuangan.master.akunkategori');
    Route::get('keuangan/master/rekanan', \App\Http\Livewire\Keuangan\Master\RekananIndex::class)->name('keuangan.master.rekanan');

    // config keuangan
    Route::get('keuangan/config/akun', \App\Http\Livewire\KonfigurasiJurnalIndex::class)->name('keuangan.config');
    // payment penjualan
    Route::get('keuangan/kasir/penjualan', \App\Http\Livewire\Keuangan\Kasir\PenerimaanPenjualanIndex::class)->name('keuangan.kasir.penjualan');
    Route::get('keuangan/kasir/penjualan/penerimaan', \App\Http\Livewire\Keuangan\Kasir\PenerimaanPenjualanForm::class)->name('keuangan.kasir.penjualan.penerimaan');
    Route::get('keuangan/kasir/penjualan/penerimaan/{penerimaanPenjualanId}', \App\Http\Livewire\Keuangan\Kasir\PenerimaanPenjualanForm::class);

    // set piutang
    Route::get('keuangan/kasir/penjualan/setpiutang')->name('keuangan.kasir.penjualan.setpiutang');

    Route::get('keuangan/jurnal/piutangpenjualan', \App\Http\Livewire\Keuangan\Kasir\DaftarPiutangPenjualan::class)->name('keuangan.jurnal.piutangpenjualan'); // daftar piutang by customer

    // payment pembelian
    Route::get('keuangan/kasir/pembelian')->name('keuangan.kasir.pembelian');
    Route::get('keuangan/kasir/pembelian/pembayaran')->name('keuangan.kasir.pembelian.pembayaran');

    Route::get('keuangan/kasir/hutangpembelian')->name('keuangan.kasir.hutangpembelian'); // daftar hutang by supplier

    // hutang pegawai
    Route::get('keuangan/kasir/piutanginternal')->name('keuangan.kasir.piutanginternal');
    Route::get('keuangan/kasir/piutanginternal/pembayaran')->name('keuangan.kasir.piutanginternal.pembayaran');
    Route::get('keuangan/kasir/piutanginternal/pembayaran/{id}');
    Route::get('keuangan/kasir/piutanginternal/penerimaan')->name('keuangan.kasir.piutanginternal.penerimaan');
    Route::get('keuangan/kasir/piutanginternal/penerimaan/{id}');

    // piutang penjualan
    Route::get('keuangan/penjualan/piutang', \App\Http\Livewire\Keuangan\Kasir\PiutangPenjualanIndex::class)->name('penjualan.piutang');
    Route::get('keuangan/penjualan/piutang/trans', \App\Http\Livewire\Keuangan\Kasir\PiutangPenjualanForm::class)->name('penjualan.piutang.trans');
    Route::get('keuangan/penjualan/piutang/trans/{jurnalSetPiutangId}', \App\Http\Livewire\Keuangan\Kasir\PiutangPenjualanForm::class)->name('penjualan.piutang.trans.piutangId');

    Route::get('keuangan/penjualan/piutanglama', \App\Http\Livewire\Keuangan\PiutangPenjualanLamaIndex::class)->name('penjualan.piutanglama');
    Route::get('keuangan/penjualan/piutanglama/trans', \App\Http\Livewire\Keuangan\PiutangPenjualanLamaForm::class)->name('penjualan.piutanglama.trans');
    Route::get('keuangan/penjualan/piutanglama/trans/{piutangLamaId}', \App\Http\Livewire\Keuangan\PiutangPenjualanLamaForm::class)->name('penjualan.piutanglama.trans.piutangLamaId');

    // penerimaan
    Route::get('keuangan/jurnal/penerimaan')->name('keuangan.jurnal.penerimaan');
    Route::get('keuangan/jurnal/penerimaan/trans')->name('keuangan.jurnal.penerimaan.trans');
    Route::get('keuangan/jurnal/penerimaan/trans/{id}');

    // pengeluaran
    Route::get('keuangan/jurnal/pengeluaran')->name('keuangan.jurnal.pengeluaran');
    Route::get('keuangan/jurnal/pengeluaran/trans')->name('keuangan.jurnal.pengeluaran.trans');
    Route::get('keuangan/jurnal/pengeluaran/trans/{id}');

    // jurnal umum
    Route::get('keuangan/jurnal/umum', \App\Http\Livewire\Keuangan\Jurnal\JurnalUmumIndex::class)->name('jurnal.umum');
    Route::get('keuangan/jurnal/umum/trans', \App\Http\Livewire\Keuangan\Jurnal\JurnalUmumForm::class)->name('jurnal.umum.trans');

    // penyesuaian
    Route::get('keuangan/jurnal/penyesuaian')->name('keuangan.jurnal.penyesuaian');
    Route::get('keuangan/jurnal/penyesuaian/trans')->name('keuangan.jurnal.penyesuaian.trans');
    Route::get('keuangan/jurnal/penyesuaian/trans/{id}');

    // Jurnal transaksi
    Route::get('keuangan/jurnal/transaksi', \App\Http\Livewire\Keuangan\Jurnal\JurnalTransaksiIndex::class)->name('jurnal.transaksi');

    // neraca
    Route::get('keuangan/neraca/awal', \App\Http\Livewire\Keuangan\Neraca\NeracaSaldoAwalIndex::class)->name('keuangan.neraca');
    Route::get('keuangan/neraca/saldo/awal', \App\Http\Livewire\Keuangan\Kasir\NeracaSaldoAwal::class)->name('keuangan.neraca.saldoawal');

    // persediaan
    Route::get('keuangan/tester/index', \App\Http\Livewire\Keuangan\Persediaan\PersediaanIndex::class)->name('keuangan.persediaan');
    Route::get('keuangan/tester/transaksi', \App\Http\Livewire\Keuangan\Persediaan\PersediaanTransaksiIndex::class)->name('keuangan.persediaan.transaksi');

    // persediaan awal temporary
    Route::get('keuangan/persediaan/awal/temp', \App\Http\Livewire\Keuangan\PersediaanTempIndex::class);

    // persediaan opname
    Route::get('keuangan/persediaan/opname', \App\Http\Livewire\Keuangan\PersediaanOpnameIndex::class)->name('persediaan.opname');
    Route::get('keuangan/persediaan/opname/trans', \App\Http\Livewire\Keuangan\PersediaanOpnameForm::class)->name('persediaan.opname.t');
    Route::get('keuangan/persediaan/opname/trans/{persediaanOpnameId}', \App\Http\Livewire\Keuangan\PersediaanOpnameForm::class)->name('persediaan.opname.trans');

    // laba-rugi
    Route::get('keuangan/labarugi')->name('keuangan.labarugi');
    Route::get('keuangan/labarugi/{closedcash}')->name('keuangan.labarugi');
});
