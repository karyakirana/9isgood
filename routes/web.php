<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pdf\ReportPdfController;
use App\Http\Livewire\Generator\Keuangan\PiutangPenjualanGenerator;
use App\Http\Livewire\Generator\{PembelianEksternal, PembelianInternal, Penjualan, PenjualanRetur, PersediaanOpname};
use App\Http\Livewire\Generator\Stock\GenStockInventory;
use App\Http\Livewire\Pembelian\{PembelianLuarForm, PembelianLuarIndex};
use App\Http\Livewire\Purchase\{PembelianReturForm, PembelianReturIndex};
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
    PenjualanReturIndex,
    ReturPenjualanForm};
use App\Http\Livewire\Testing\{
    TestingPenjualanForm,
    TestingPenjualanIndex,
    };
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
Route::get('/test/kasir/penerimaan/{id}', [\App\Http\Controllers\Pdf\ReportController::class, 'PenerimaanPenjualanPrintOut']);
Route::get('/test/kasir/pengeluaran/{id}', [\App\Http\Controllers\Pdf\ReportPengeluaranController::class, 'PengeluaranPembelianPrintOut']);

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

    Route::get('/master/pihakketiga', \App\Http\Livewire\Master\PersonRelationIndex::class)->name('pihakketiga');
});

Route::middleware('auth')->group(function (){

    // closed cash
    Route::get('closedcash', CloseCashIndex::class)->name('closedcash');

    // config hpp\
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
    Route::get('penjualan/retur/{kondisi}/trans/{penjualanReturId}', ReturPenjualanForm::class);

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
    Route::controller(AuthController::class)->group(function (){
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

Route::get('/logout', [AuthController::class, 'destroy'])->name('logout');

// require __DIR__.'/auth.php';
 require __DIR__.'/keuangan.php';
 require __DIR__.'/stockRoute.php';
 require __DIR__.'/testerRoute.php';
