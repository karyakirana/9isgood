<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('saldo_piutang_penjualan', function (Blueprint $table) {
            $table->dropColumn(['tgl_awal', 'tgl_akhir']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('saldo_piutang_penjualan', function (Blueprint $table) {
            $table->date('tgl_awal')->after('customer_id');
            $table->date('tgl_akhir')->nullable()->after('tgl_awal');
        });
    }
};
