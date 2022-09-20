<?php

use App\Http\Controllers\Kasir\PiutangPenjualanController;
use App\Http\Controllers\Keuangan\Jurnal\JurnalPersediaanController;
use App\Http\Controllers\Keuangan\JurnalPiutangPenjualanAwalController;
use App\Http\Controllers\Keuangan\Neraca\PiutangPenjualanAwalController;
use App\Http\Controllers\Keuangan\PersediaanController;
use App\Http\Livewire\Config\ConfigJurnalForm;
use App\Http\Livewire\Keuangan\Jurnal\{JurnalTransaksiIndex, JurnalUmumForm, JurnalUmumIndex};
use App\Http\Livewire\Keuangan\{JurnalSetPiutangReturForm, JurnalSetPiutangReturIndex };
use App\Http\Livewire\Keuangan\Neraca\{NeracaSaldoAwalIndex, NeracaSaldoIndex};
use App\Http\Livewire\Keuangan\Persediaan\{GenerateOpnameFromStockOpname,
    GenerateOpnamePrice,
    PersediaanIndex,
    PersediaanTransaksiIndex};
use App\Http\Livewire\Keuangan\{GenerateReturToHutang,
    GenerateReturToPiutang,
    PersediaanOpnameForm,
    PersediaanOpnameIndex,
    PersediaanTempIndex,
    PiutangPenjualanLamaForm,
    PiutangPenjualanLamaIndex,
    SaldoPiutangIndex};
use App\Http\Livewire\Keuangan\Kasir\{DaftarPiutangPenjualan,
    NeracaSaldoAwal,
    PenerimaanPenjualanForm,
    PenerimaanPenjualanIndex,
    PiutangPenjualanForm,
    PiutangPenjualanIndex,
    PiutangPenjualanReturIndex};
use App\Http\Livewire\Keuangan\Master\{AkunIndex, AkunKategoriIndex, AkunTipeIndex, RekananIndex};
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (){

    // master keuangan
    Route::get('keuangan/master/akun', AkunIndex::class)->name('keuangan.master.akun');
    Route::get('keuangan/master/akuntipe', AkunTipeIndex::class)->name('keuangan.master.akuntipe');
    Route::get('keuangan/master/akunkategori', AkunKategoriIndex::class)->name('keuangan.master.akunkategori');
    Route::get('keuangan/master/rekanan', RekananIndex::class)->name('keuangan.master.rekanan');

    // config keuangan
    Route::get('config/akun', ConfigJurnalForm::class)->name('config');

    // set piutang
    Route::get('kasir/penjualan/setpiutang')->name('keuangan.kasir.penjualan.setpiutang');

    // kasir - penerimaan
    Route::get('kasir/penerimaan/penjualan', PenerimaanPenjualanIndex::class)->name('kasir.penerimaan.penjualan');
    Route::get('kasir/penerimaan/penjualan/baru', PenerimaanPenjualanForm::class)->name('kasir.penerimaan.penjualan.baru');
    Route::get('kasir/penerimaan/piutangpenjualan', DaftarPiutangPenjualan::class)->name('kasir.piutang.penjualan');
    Route::get('kasir/penerimaan/piutangpenjualan/{customer_id}', [PiutangPenjualanController::class, 'showDetailPenjualan'])
        ->name('kasir.piutang.penjualan.detail');
    Route::get('kasir/penerimaan/penjualan/print/{penerimaan_id}')
        ->name('kasir.penerimaan.penjualan.print');

    // kasir - pengeluaran
    Route::get('kasir/pengeluaran/pembelian')->name('kasir.pengeluaran.pembelian');
    Route::get('kasir/pengeluaran/pembelian/form')->name('kasir.pengeluaran.pembelian.form');
    Route::get('kasir/pengeluaran/hutangpembelian/{supplier_id}')->name('kasir.pengeluaran.hutangpembelian.detail');

    // kasir - daftar mutasi rekening

    // kasir - daftar piutang internal
    Route::get('kasir/piutanginternal')->name('kasir.piutang.internal');

    // kasir - daftar hutang
    Route::get('kasir/hutang/pembelian');


    // payment pembelian
    Route::get('kasir/pembelian')->name('keuangan.kasir.pembelian');
    Route::get('kasir/pembelian/pembayaran')->name('keuangan.kasir.pembelian.pembayaran');

    Route::get('kasir/hutangpembelian')->name('keuangan.kasir.hutangpembelian'); // daftar hutang by supplier

    // hutang pegawai
    Route::get('kasir/piutanginternal')->name('keuangan.kasir.piutanginternal');
    Route::get('kasir/piutanginternal/pembayaran')->name('keuangan.kasir.piutanginternal.pembayaran');
    Route::get('kasir/piutanginternal/pembayaran/{id}');
    Route::get('kasir/piutanginternal/penerimaan')->name('keuangan.kasir.piutanginternal.penerimaan');
    Route::get('kasir/piutanginternal/penerimaan/{id}');

    // generate retur penjualan to piutang
    Route::get('kasir/generate/returtopiutang', GenerateReturToPiutang::class)->name('generate.returtopiutang');
    // generate retur pembelian
    Route::get('kasir/generate/returtohutang', GenerateReturToHutang::class);


    // saldo piutang penjualan
    Route::get('keuangan/penjualan/saldopiutang', SaldoPiutangIndex::class)->name('penjualan.saldopiutang');

    // piutang awal
    // piutang dari penjualan lama
    // piutang dari retur penjualan lama

    // penerimaan
    Route::get('keuangan/jurnal/penerimaan')->name('keuangan.jurnal.penerimaan');
    Route::get('keuangan/jurnal/penerimaan/trans')->name('keuangan.jurnal.penerimaan.trans');
    Route::get('keuangan/jurnal/penerimaan/trans/{id}');

    // pengeluaran
    Route::get('keuangan/jurnal/pengeluaran')->name('keuangan.jurnal.pengeluaran');
    Route::get('keuangan/jurnal/pengeluaran/trans')->name('keuangan.jurnal.pengeluaran.trans');
    Route::get('keuangan/jurnal/pengeluaran/trans/{id}');

    /**
     * Keuangan
     */

    // neraca awal
    Route::get('keuangan/neraca/awal', NeracaSaldoAwalIndex::class)->name('keuangan.neraca.awal');
    // halaman index piutang awal
    Route::get('keuangan/neraca/awal/piutang', [PiutangPenjualanAwalController::class, 'index'])
        ->name('keuangan.neraca.awal.piutang');
    // piutang penjualan awal
    Route::get('keuangan/neraca/awal/piutang-penjualan', PiutangPenjualanIndex::class)
        ->name('keuangan.neraca.awal.piutang-penjualan');
    Route::get('keuangan/neraca/awal/piutang-penjualan/baru', [JurnalPiutangPenjualanAwalController::class, 'createForPenjualan'])
        ->name('keuangan.neraca.awal.piutang-penjualan.baru');
    Route::get('keuangan/neraca/awal/piutang-penjualan/edit/{id}', [JurnalPiutangPenjualanAwalController::class, 'edit'])
        ->name('keuangan.neraca.awal.piutang-penjualan.edit');
    // piutang penjualan retur awal
    Route::get('keuangan/neraca/awal/piutang-retur', PiutangPenjualanReturIndex::class)->name('keuangan.neraca.awal.piutang-retur');
    Route::get('keuangan/neraca/awal/piutang-retur/baru', [JurnalPiutangPenjualanAwalController::class, 'createForRetur'])->name('keuangan.neraca.awal.piutang-retur.baru');
    Route::get('keuangan/neraca/awal/piutang-retur/edit/{id}', [JurnalPiutangPenjualanAwalController::class, 'edit'])->name('keuangan.neraca.awal.piutang-retur.edit');
    // halaman index hutang awal
    Route::get('keuangan/neraca/awal/hutang')->name('keuangan.neraca.awal.hutang');
    // hutang pembelian awal
    Route::get('keuangan/neraca/awal/hutang-pembelian')->name('keuangan.neraca.awal.hutang-pembelian');
    // hutang retur awal
    Route::get('keuangan/neraca/awal/hutang-retur')->name('keuangan.neraca.awal.hutang-retur');
    // persediaan awal
    Route::get('keuangan/neraca/awal/persediaan')->name('neraca.persediaan');
    Route::get('keuangan/neraca/awal/persediaan/buku-internal')->name('neraca.persediaan.buku-internal');
    Route::get('keuangan/neraca/awal/persediaan/buku-luar')->name('neraca.persediaan.buku-luar');

    // neraca saldo
    Route::get('keuangan/neraca/saldo/index', NeracaSaldoIndex::class)->name('keuangan.neraca.saldo');

    // jurnal transaksi
    Route::get('keuangan/jurnal/transaksi', JurnalTransaksiIndex::class)->name('jurnal.transaksi');

    // jurnal persediaan
    Route::get('keuangan/jurnal/persediaan', [JurnalPersediaanController::class, 'indexPersediaan'])->name('persediaan');
    Route::get('keuangan/jurnal/persediaan/jurnal', [JurnalPersediaanController::class, 'index'])->name('persediaan.jurnal');

    // laba rugi
    Route::get('keuangan/labarugi')->name('keuangan.labarugi');
    Route::get('keuangan/labarugi/periode')->name('keuangan.labarugi.periode');
    Route::get('keuangan/labarugi/bydate')->name('keuangan.labarugi.bydate');

    // jurnal umum
    Route::get('keuangan/jurnal/umum', JurnalUmumIndex::class)->name('jurnal.umum');
    Route::get('keuangan/jurnal/umum/trans', JurnalUmumForm::class)->name('jurnal.umum.trans');

    // penyesuaian
    Route::get('keuangan/jurnal/penyesuaian')->name('keuangan.jurnal.penyesuaian');
    Route::get('keuangan/jurnal/penyesuaian/trans')->name('keuangan.jurnal.penyesuaian.trans');
    Route::get('keuangan/jurnal/penyesuaian/trans/{id}');

    // persediaan
    Route::get('keuangan/tester/index', PersediaanIndex::class)->name('keuangan.persediaan');
    Route::get('keuangan/tester/transaksi', PersediaanTransaksiIndex::class)->name('keuangan.persediaan.transaksi');

    // persediaan awal temporary
    Route::get('keuangan/persediaan/awal/temp', PersediaanTempIndex::class);

    // laba-rugi
    Route::get('keuangan/labarugi')->name('keuangan.labarugi');
    Route::get('keuangan/labarugi/{closedcash}')->name('keuangan.labarugi');

    // neraca menu

    // neraca
    Route::get('neraca/awal', NeracaSaldoAwalIndex::class)->name('keuangan.neraca');
    Route::get('neraca/saldo/awal', NeracaSaldoAwal::class)->name('keuangan.neraca.saldoawal');

    // neraca piutang
    Route::get('neraca/asset/penjualan/piutang', PiutangPenjualanIndex::class)->name('penjualan.piutang');
    Route::get('neraca/asset/penjualan/piutang/trans', PiutangPenjualanForm::class)->name('penjualan.piutang.trans');
    Route::get('neraca/asset/penjualan/piutang/trans/{jurnalSetPiutangId}', PiutangPenjualanForm::class)->name('penjualan.piutang.trans.piutangId');

    Route::get('neraca/asset/penjualan/piutanglama', PiutangPenjualanLamaIndex::class)->name('penjualan.piutanglama');
    Route::get('neraca/asset/penjualan/piutanglama/trans', PiutangPenjualanLamaForm::class)->name('penjualan.piutanglama.trans');
    Route::get('neraca/asset/penjualan/piutanglama/trans/{piutangLamaId}', PiutangPenjualanLamaForm::class)->name('penjualan.piutanglama.trans.piutangLamaId');

    // neraca persediaan opname
    Route::get('persediaan/awal/stockopname', PersediaanOpnameIndex::class)->name('persediaan.awal.stockopname');
    Route::get('neraca/asset/persediaan/opname/trans', PersediaanOpnameForm::class)->name('persediaan.opname.t');
    Route::get('neraca/asset/persediaan/opname/trans/{persediaanOpnameId}', PersediaanOpnameForm::class)->name('persediaan.opname.trans');


    // kasir

    // kasir hutang penjualan retur
    Route::get('kasir/penjualan/piutangretur', JurnalSetPiutangReturIndex::class)->name('penjualan.piutangretur');
    Route::get('kasir/penjualan/piutangretur/trans', JurnalSetPiutangReturForm::class)->name('penjualan.piutangretur.trans');
    Route::get('kasir/penjualan/piutangretur/trans/{jurnalSetPiutangRetur}', JurnalSetPiutangReturForm::class)->name('penjualan.piutangretur.trans.edit');


    // kasir payment penjualan
    Route::get('kasir/penjualan', PenerimaanPenjualanIndex::class)->name('keuangan.kasir.penjualan');
    Route::get('kasir/penjualan/penerimaan', PenerimaanPenjualanForm::class)->name('keuangan.kasir.penjualan.penerimaan');
    Route::get('kasir/penjualan/penerimaan/{penerimaanPenjualanId}', PenerimaanPenjualanForm::class);

    // kasir piutang penjualan
    Route::get('kasir/jurnal/piutangpenjualan', DaftarPiutangPenjualan::class)->name('keuangan.jurnal.piutangpenjualan'); // daftar piutang by customer

    // keuangan persediaan log
    Route::get('persediaan/log')->name('persediaan.log');
    Route::get('persediaan/log/inventory', [PersediaanController::class, 'log'])->name('persediaan.log.transaksi');
    Route::get('persediaan/log/opname', GenerateOpnameFromStockOpname::class)->name('persediaan.log.opname');
    Route::get('persediaan/log/opnameprice', GenerateOpnamePrice::class)->name('persediaan.log.opname.price');

});
