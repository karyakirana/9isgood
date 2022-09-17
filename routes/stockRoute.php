<?php

/**
 * Stock Routing
 */

use App\Http\Controllers\Stock\StockLogController;
use App\Http\Controllers\Stock\StockMutasiController;
use App\Http\Controllers\Stock\StockOpnameController;
use App\Http\Livewire\Pembelian\PembelianInternalForm;
use App\Http\Livewire\Pembelian\PembelianInternalIndex;
use App\Http\Livewire\Stoc\StockRusakForm;
use App\Http\Livewire\Stoc\StockRusakIndex;
use App\Http\Livewire\Stock\InventoryByJenisIndex;
use App\Http\Livewire\Stock\InventoryIndex;
use App\Http\Livewire\Stock\Mutasi\StockMutasiBaikBaikIndeks;
use App\Http\Livewire\Stock\Mutasi\StockMutasiBaikBaikTrans;
use App\Http\Livewire\Stock\Mutasi\StockMutasiBaikRusakIndeks;
use App\Http\Livewire\Stock\Mutasi\StockMutasiBaikRusakTrans;
use App\Http\Livewire\Stock\Mutasi\StockMutasiRusakRusakIndeks;
use App\Http\Livewire\Stock\Mutasi\StockMutasiRusakRusakTrans;
use App\Http\Livewire\Stock\RefreshStock;
use App\Http\Livewire\Stock\StockAkhirForm;
use App\Http\Livewire\Stock\StockAkhirIndex;
use App\Http\Livewire\Stock\StockCardIndex;
use App\Http\Livewire\Stock\StockKeluarForm;
use App\Http\Livewire\Stock\StockKeluarIndex;
use App\Http\Livewire\Stock\StockMasukInternalIndex;
use App\Http\Livewire\Stock\StockMutasiBaikBaikForm;
use App\Http\Livewire\Stock\StockMutasiBaikBaikIndex;
use App\Http\Livewire\Stock\StockMutasiBaikRusakForm;
use App\Http\Livewire\Stock\StockMutasiBaikRusakIndex;
use App\Http\Livewire\Stock\StockMutasiForm;
use App\Http\Livewire\Stock\StockMutasiIndex;
use App\Http\Livewire\Stock\StockMutasiRusakRusakForm;
use App\Http\Livewire\Stock\StockMutasiRusakRusakIndex;
use App\Http\Livewire\Stock\StockOpnameForm;
use App\Http\Livewire\Stock\StockOpnameIndex;
use App\Http\Livewire\Stock\StockOpnameKoreksiForm;
use App\Http\Livewire\Stock\StockOpnameKoreksiIndex;
use App\Http\Livewire\Testing\StockCardTestTable;
use App\Http\Livewire\Testing\TestingStockMasukIndex;
use App\Http\Livewire\Testing\TestingStockMutasiForm;

Route::middleware('auth')->group(function (){

    // stock report
    Route::get('stock/report', RefreshStock::class);

    // stock index
    Route::get('stock/log', [StockLogController::class, 'index'])->name('stock.index');
    Route::get('stock/log/inventory', [StockLogController::class, 'inventory'])->name('stock.index');

    // daftar inventory
    Route::get('stock/inventory', InventoryIndex::class)->name('inventory');
    Route::get('stock/inventory/{jenis}/{gudang}', InventoryByJenisIndex::class);

    // card stock
    Route::get('stock/card/{produk_id}', StockCardIndex::class);
    // Route::get('stock/card/{produk_id}/{gudang_id}', \App\Http\Livewire\Stock\StockCardIndex::class)->name('stock.card');
    Route::get('stock/card/{produk_id}/{gudang_id}', StockCardTestTable::class)->name('stock.card');

    Route::get('stock/print/stockopname', [StockOpnameController::class, 'reportStockByProduk'])->name('stock.print.stockopname');

    // stock opname
    Route::get('stock/opname/koreksi', StockOpnameKoreksiIndex::class)->name('stock.opname.koreksi');
    Route::get('stock/opname/koreksi/form/{jenis}', StockOpnameKoreksiForm::class)->name('stock.opname.koreksi.form');
    Route::get('stock/opname/koreksi/form/{jenis}/{stockOpnameKoreksiId}', StockOpnameKoreksiForm::class)->name('stock.opname.koreksi.form.edit');

    // testing stock masuk
    Route::get('testing/stockmasuk/index', TestingStockMasukIndex::class)->name('testing.stockmasuk.index');
    Route::get('testing/stockmasuk/form', PembelianInternalForm::class)->name('testing.stockmasuk.form');

    // testing stock mutasi
    Route::get('testing/stock/transaksi/mutasi/baik_baik/trans',  TestingStockMutasiForm::class)->name('testing.stock.mutasi.baikbaik.trans');


    // stock transaksi
    Route::get('stock/masuk', PembelianInternalIndex::class)->name('stock.masuk');
    Route::get('stock/masuk/form', PembelianInternalForm::class)->name('stock.masuk.trans');
    Route::get('stock/masuk/form/{pembelianId}', PembelianInternalForm::class)->name('stock.masuk.trans.edit');

    Route::get('stock/keluar', StockKeluarIndex::class);

    // jatah dihapus
    Route::get('stock/transaksi/keluar', StockKeluarIndex::class)->name('stock.keluar');
    Route::get('stock/transaksi/keluar/{kondisi}', StockKeluarIndex::class);
    Route::get('stock/transaksi/keluar/trans/{kondisi}', StockKeluarForm::class);
    Route::get('stock/transaksi/keluar/trans/{kondisi}/{stockkeluar}', StockKeluarForm::class);

    // stock mutasi
    Route::get('stock/mutasi', [StockMutasiController::class, 'index'])->name('stock.mutasi');
    Route::get('stock/mutasi/report/{kondisi}', [StockMutasiController::class, 'jenisMutasi'])->name('stock.mutasi.kondisi');
    Route::get('stock/mutasi/form', StockMutasiForm::class)->name('stock.mutasi.form');
    Route::get('stock/mutasi/form/{mutasiId}', StockMutasiForm::class)->name('stock.mutasi.form.edit');

    // stock mutasi dari baik ke rusak
    Route::get('stock/rusak', StockRusakIndex::class)->name('stock.rusak');
    Route::get('stock/rusak/trans', StockRusakForm::class)->name('stock.rusak.trans');

    Route::get('stock/transaksi/opname', StockOpnameIndex::class)->name('stock.opname');
    Route::get('stock/transaksi/opname/{jenis}', StockOpnameIndex::class);
    Route::get('stock/transaksi/opname/trans/{jenis}', StockOpnameForm::class);
    Route::get('stock/transaksi/opname/trans/{jenis}/{stockOpname_id}', StockOpnameForm::class);

    Route::get('stock/transaksi/mutasi', StockMutasiIndex::class)->name('mutasi');
    Route::get('stock/transaksi/mutasi/edit/{mutasiId}', StockMutasiForm::class)->name('mutasi.trans.edit');
    Route::get('stock/transaksi/mutasi/baik_baik', StockMutasiIndex::class)->name('mutasi.baik_baik');
    Route::get('stock/transaksi/mutasi/baik_baik/trans', StockMutasiForm::class)->name('mutasi.baik_baik.trans');
    Route::get('stock/transaksi/mutasi/baik_rusak', StockMutasiIndex::class)->name('mutasi.baik_rusak');
    Route::get('stock/transaksi/mutasi/baik_rusak/trans', StockMutasiForm::class)->name('mutasi.baik_rusak.trans');
    Route::get('stock/transaksi/mutasi/rusak_rusak', StockMutasiIndex::class)->name('mutasi.rusak_rusak');
    Route::get('stock/transaksi/mutasi/rusak_rusak/trans', StockMutasiForm::class)->name('mutasi.rusak_rusak.trans');

    Route::get('stock/transaksi/mutasi/baik/baik',  StockMutasiBaikBaikIndex::class)->name('stock.mutasi.baik.baik');
    Route::get('stock/transaksi/mutasi/baik/baik/trans',  StockMutasiBaikBaikForm::class)->name('stock.mutasi.baik.baik.trans');
    Route::get('stock/transaksi/mutasi/baik/baik/trans/{mutasiId}',  StockMutasiBaikBaikForm::class)->name('stock.mutasi.baik.baik.trans.edit');
    Route::get('stock/transaksi/mutasi/baik/rusak',  StockMutasiBaikRusakIndex::class)->name('stock.mutasi.baik.rusak');
    Route::get('stock/transaksi/mutasi/baik/rusak/trans',  StockMutasiBaikRusakForm::class)->name('stock.mutasi.baik.rusak.trans');
    Route::get('stock/transaksi/mutasi/rusak/rusak',  StockMutasiRusakRusakIndex::class)->name('stock.mutasi.rusak.rusak');
    Route::get('stock/transaksi/mutasi/rusak/rusak/trans',  StockMutasiRusakRusakForm::class)->name('stock.mutasi.rusak.rusak.trans');

    // mutasi new
    Route::get('stock/mutasi/baik/baik', StockMutasiBaikBaikIndeks::class)->name('');
    Route::get('stock/mutasi/baik/baik/trans', StockMutasiBaikBaikTrans::class);
    Route::get('stock/mutasi/baik/rusak', StockMutasiBaikRusakIndeks::class)->name('');
    Route::get('stock/mutasi/baik/rusak/trans', StockMutasiBaikRusakTrans::class);
    Route::get('stock/mutasi/rusak/rusak', StockMutasiRusakRusakIndeks::class)->name('');
    Route::get('stock/mutasi/rusak/rusak/trans', StockMutasiRusakRusakTrans::class);

    // numpang stock
    Route::get('stock/stockakhir', StockAkhirIndex::class)->name('stock.stockakhir');
    Route::get('stock/stockakhir/transaksi', StockAkhirForm::class)->name('stock.stockakhir.transaksi');
    Route::get('stock/stockakhir/transaksi/{id}', StockAkhirForm::class);

    Route::get('stock/transaksi/internal', StockMasukInternalIndex::class)->name('stock.masuk.internal.index');
});
