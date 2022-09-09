<?php

use App\Http\Controllers\Pdf\ReportPdfController;
use App\Http\Livewire\Generator\Keuangan\PiutangPenjualanGenerator;
use App\Http\Livewire\Generator\{PembelianEksternal, PembelianInternal, Penjualan, PenjualanRetur, PersediaanOpname};
use App\Http\Livewire\Generator\Stock\GenStockInventory;
use App\Http\Livewire\Stoc\{StockRusakForm, StockRusakIndex};
use App\Http\Controllers\Stock\{StockLogController, StockMutasiController, StockOpnameController};
use App\Http\Livewire\Pembelian\{PembelianLuarForm, PembelianLuarIndex};
use App\Http\Livewire\Purchase\{PembelianInternalForm, PembelianInternalIndex, PembelianReturForm, PembelianReturIndex};
use App\Http\Livewire\Stock\{InventoryByJenisIndex,
    InventoryIndex,
    Mutasi\StockMutasiBaikBaikIndeks,
    Mutasi\StockMutasiBaikBaikTrans,
    Mutasi\StockMutasiBaikRusakIndeks,
    Mutasi\StockMutasiBaikRusakTrans,
    Mutasi\StockMutasiRusakRusakIndeks,
    Mutasi\StockMutasiRusakRusakTrans,
    RefreshStock,
    StockAkhirForm,
    StockAkhirIndex,
    StockCardIndex,
    StockKeluarForm,
    StockKeluarIndex,
    StockMasukInternalIndex,
    StockMutasiBaikBaikForm,
    StockMutasiBaikBaikIndex,
    StockMutasiBaikRusakForm,
    StockMutasiBaikRusakIndex,
    StockMutasiForm,
    StockMutasiIndex,
    StockMutasiRusakRusakForm,
    StockMutasiRusakRusakIndex,
    StockOpnameForm,
    StockOpnameIndex};
use App\Http\Controllers\Penjualan\{PenjualanReturReportController, ReportPenjualanController};
use App\Http\Controllers\Sales\ReceiptController;
use App\Http\Controllers\Testing\{
    PersediaanController,TestController, TestingPenjualanToPersediaan, TestingPenjualanToStockMasuk, TestingStockMutasi
};
use App\Http\Livewire\CloseCashIndex;
use App\Http\Livewire\Config\{ConfigHpp, ConfigJurnalForm};
use App\Http\Livewire\Master\CustomerIndex;
use App\Http\Livewire\Penjualan\{PenjualanForm,
    PenjualanIndex,
    PenjualanReportIndex,
    PenjualanReturForm,
    PenjualanReturIndex,
    ReturPenjualanForm};
use App\Http\Livewire\Testing\{StockCardTestTable,
    TestingPenjualanForm,
    TestingPenjualanIndex,
    TestingStockMasukIndex,
    TestingStockMutasiForm};
use App\Http\Livewire\Master\{
    GudangIndex,
    PegawaiIndex,
    PegawaiUserIndex,
    ProdukIndex,
    ProdukKategoriHargaIndex,
    ProdukKategoriIndex,
    SupplierIndex,
    SupplierJenisIndex
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard.index-new');
})->middleware(['auth'])->name('dashboard');

//Route::get('/metronics', function (){
//    return view('pages.dashboard.index');
//});

/**
 * Master Routing
 */
Route::middleware('auth')->group(function (){
    Route::get('/master/produk', ProdukIndex::class)->name('produk');
    Route::get('/master/produk/kategori', ProdukKategoriIndex::class)->name('produk.kategori');
    Route::get('/master/produk/kategoriharga', ProdukKategoriHargaIndex::class)->name('produk.kategoriharga');

    Route::get('/master/gudang', GudangIndex::class)->name('gudang');
    Route::get('/master/customer', CustomerIndex::class)->name('customer');
    Route::get('/master/supplier', SupplierIndex::class)->name('supplier');
    Route::get('/master/supplier/jenis', SupplierJenisIndex::class)->name('supplier.jenis');

    Route::get('/master/pegawai', PegawaiIndex::class)->name('pegawai');
    Route::get('/master/pegawai/user', PegawaiUserIndex::class)->name('pegawai.user');
});

Route::middleware('auth')->group(function (){

    // closed cash
    Route::get('closedcash', CloseCashIndex::class)->name('closedcash');

    // config hpp
    Route::get('keuangan/config/hpp', ConfigHpp::class)->name('config.hpp');

    // config jurnal
    Route::get('keuangan/config/jurnal', ConfigJurnalForm::class)->name('config.jurnal');

    // testing
    Route::get('testing/persediaan/get', [PersediaanController::class, 'persediaanOut']);

});

/**
 * Penjualan Routing
 */
Route::middleware('auth')->group(function (){

    // penjualan
    Route::get('penjualan', PenjualanIndex::class)->name('penjualan');
    Route::get('penjualan/trans', PenjualanForm::class)->name('penjualan.trans');
    Route::get('penjualan/trans/{penjualanId}', PenjualanForm::class);

    // testing stock mutasi
    Route::get('testing/stockmutasi', [TestingStockMutasi::class, 'testingstockMutasi']);

    // testing penjualan
    Route::get('testing/penjualan', TestingPenjualanIndex::class)->name('testingpenjualan');
    Route::get('testing/penjualan/trans', TestingPenjualanForm::class)->name('testingpenjualan.trans');
    Route::get('testing/generate/penjualan/stockmasuk', [TestingPenjualanToStockMasuk::class, 'testinggeneratePenjualan']);
    Route::get('testing/generate/penjualan/persediaan', [TestingPenjualanToPersediaan::class, 'testingPenjualanToPersediaan']);

    Route::get('testing', [TestController::class, 'index']);


    Route::get('penjualan/print/{penjualan}', [ReceiptController::class, 'penjualanDotMatrix']);

    Route::get('penjualan/pdf/{penjualan}/report', [ReportPdfController::class, 'penjualanPdf']);

    Route::get('penjualan/retur/{kondisi}', PenjualanReturIndex::class);
    Route::get('penjualan/retur/{kondisi}/trans', ReturPenjualanForm::class);
    Route::get('penjualan/retur/{kondisi}/trans/{retur}', ReturPenjualanForm::class);

    Route::get('penjualan/retur/print/{penjualanRetur}', [ReceiptController::class, 'penjualanReturDotMatrix']);

    // report penjualan
    Route::get('penjualan/report', PenjualanReportIndex::class)->name('penjualan.report');
    // Route::get('penjualan/report/bydate', \App\Http\Livewire\Penjualan\ReportPenjualanByDateForm::class)->name('penjualan.report.bydate');
    Route::get('penjualan/report/bydate/{tglAwal}/{tglAkhir}', [ReportPenjualanController::class, 'reportByDate'])->name('penjualan.report.bydate');
    Route::get('penjualan/report/bymonth/{bulan}', [ReportPenjualanController::class, 'reportByMonth'])->name('penjualan.report.bymonth');
    Route::get('penjualan/report/retur/{tglAwal}/{tglakhir}', [PenjualanReturReportController::class, 'reportRetur'])->name('penjualan.report.retur');
});

Route::middleware('auth')->group(function(){

    // pembelian
    Route::get('pembelian', PembelianLuarIndex::class)->name('pembelian');
    Route::get('pembelian/trans', PembelianLuarForm::class)->name('pembelian.trans');
    Route::get('pembelian/trans/{pembelianId}', PembelianLuarForm::class)->name('pembelian.trans.edit');

    Route::get('pembelian/retur/{kondisi}', PembelianReturIndex::class)->name('pembelian.retur');
    Route::get('pembelian/retur/{kondisi}/trans/', PembelianReturForm::class);
    Route::get('pembelian/retur/trans/{retur}', PembelianReturForm::class);

    // pembelian (dari buku internal)
    Route::get('pembelian/internal', PembelianInternalIndex::class)->name('pembelian.internal');
    Route::get('pembelian/internal/trans', PembelianInternalForm::class);
    Route::get('pembelian/internal/trans/{pembelian}', PembelianInternalForm::class);
});

/**
 * Stock Routing
 */
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
    Route::get('stock/opname/koreksi', \App\Http\Livewire\Stock\StockOpnameKoreksiIndex::class)->name('stock.opname.koreksi');
    Route::get('stock/opname/koreksi/form/{jenis}', \App\Http\Livewire\Stock\StockOpnameKoreksiForm::class)->name('stock.opname.koreksi.form');
    Route::get('stock/opname/koreksi/form/{jenis}/{stockOpnameKoreksiId}', \App\Http\Livewire\Stock\StockOpnameKoreksiForm::class)->name('stock.opname.koreksi.form.edit');

    // testing stock masuk
    Route::get('testing/stockmasuk/index', TestingStockMasukIndex::class)->name('testing.stockmasuk.index');
    Route::get('testing/stockmasuk/form', \App\Http\Livewire\Pembelian\PembelianInternalForm::class)->name('testing.stockmasuk.form');

    // testing stock mutasi
    Route::get('testing/stock/transaksi/mutasi/baik_baik/trans',  TestingStockMutasiForm::class)->name('testing.stock.mutasi.baikbaik.trans');


    // stock transaksi
    Route::get('stock/masuk', \App\Http\Livewire\Pembelian\PembelianInternalIndex::class)->name('stock.masuk');
    Route::get('stock/masuk/form', \App\Http\Livewire\Pembelian\PembelianInternalForm::class)->name('stock.masuk.trans');
    Route::get('stock/masuk/form/{pembelianId}', \App\Http\Livewire\Pembelian\PembelianInternalForm::class)->name('stock.masuk.trans.edit');

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

/**
 * Generator
 */
Route::middleware('auth')->group(function (){
    Route::get('generator/persediaan/stockopname', PersediaanOpname::class)->name('generator.persediaan.stockopname');
    Route::get('generator/persediaan/pembelian-internal', PembelianInternal::class)->name('generator.persediaan.pembelian-internal');
    Route::get('generator/persediaan/pembelian-eksternal', PembelianEksternal::class)->name('generator.persediaan.pembelian-eksternal');
    Route::get('generator/persediaan/penjualan', Penjualan::class)->name('generator.persediaan.penjualan');
    Route::get('generator/persediaan/penjualan-retur', PenjualanRetur::class)->name('generator.persediaan.penjualan-retur');

    Route::get('generator/stock/inventory', GenStockInventory::class)->name('generator.stock.inventory');

    // keuangan
    Route::get('generator/keuangan/piutangpenjualan', PiutangPenjualanGenerator::class)->name('generator.keuangan.piutangpenjualan');
});

/**
 * Auth Routing
 */
Route::middleware('guest')->group(function (){
    Route::controller(\App\Http\Controllers\AuthController::class)->group(function (){
        Route::get('/login', 'index')->name('login');
        Route::post('/login', 'login');
        Route::get('/register', 'create')->name('register');
        Route::post('/register', 'store');
    });
});

/**
 * Tester
 */
Route::middleware('auth')->group(function (){
    Route::get('testyoman', \App\Http\Livewire\Z\Tester::class);
});

Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'destroy'])->name('logout');

// require __DIR__.'/auth.php';
 require __DIR__.'/keuangan.php';
 require __DIR__.'/testerRoute.php';
