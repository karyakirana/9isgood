<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (){

    // tester persediaan transaksi
    Route::get('test/persediaan')->name('persediaan');
    Route::get('test/persediaan/index', \App\Http\Livewire\Test\PersediaanIndex::class)->name('test.persediaan.index');
    Route::get('test/persediaan/transaksi', \App\Http\Livewire\Test\PersediaanForm::class)->name('test.persediaan.transaksi');
    Route::get('test/persediaan/transaksi/{persediaan}', \App\Http\Livewire\Test\PersediaanForm::class)->name('test.persediaan.transaksi.transaksiId');

    // tester penjualan
    Route::get('test/penjualan/baru', \App\Http\Livewire\Test\PenjualanForm::class);
});
