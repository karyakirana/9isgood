<?php

namespace Database\Seeders;

use App\Models\KonfigurasiJurnal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KonfigurasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        KonfigurasiJurnal::query()->insertOrIgnore([
            ['config'=>'biaya_pembelian', 'kategori'=>'pembelian'],
            ['config'=>'hutang_dagang', 'kategori'=>'pembelian'],
            ['config'=>'hutang_dagang_internal', 'kategori'=>'pembelian'],
            ['config'=>'ppn_pembelian', 'kategori'=>'pembelian'],
            ['config'=>'penjualan', 'kategori'=>'penjualan'],
            ['config'=>'penjualan_retur', 'kategori'=>'penjualan'],
            ['config'=>'biaya_penjualan', 'kategori'=>'penjualan'],
            ['config'=>'piutang_usaha', 'kategori'=>'penjualan'],
            ['config'=>'ppn_penjualan', 'kategori'=>'penjualan'],
            ['config'=>'modal_piutang_awal', 'kategori'=>'penjualan'],
            ['config'=>'persediaan_baik_kalimas', 'kategori'=>'persediaan'],
            ['config'=>'persediaan_baik_perak', 'kategori'=>'persediaan'],
            ['config'=>'persediaan_rusak_kalimas', 'kategori'=>'persediaan'],
            ['config'=>'persediaan_rusak_perak', 'kategori'=>'persediaan'],
            ['config'=>'akun_persediaan_awal_kalimas', 'kategori'=>'persediaan_awal'],
            ['config'=>'akun_persediaan_awal_perak', 'kategori'=>'persediaan_awal'],
            ['config'=>'prive_modal_awal', 'kategori'=>'persediaan_awal'],
            ['config'=>'hpp_internal', 'kategori'=>'hpp'],
            ['config'=>'hpp_buku_luar', 'kategori'=>'hpp'],
            ['config'=>'modal_piutang_awal', 'kategori'=>'penjualan'],
            ['config'=>'piutang_internal', 'kategori'=>'piutang_internal'],
        ]);
    }
}
