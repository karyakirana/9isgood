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
        Schema::connection('mysql2')->table('penerimaan_penjualan_detail', function (Blueprint $table) {
            $table->dropColumn('piutang_penjualan_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('penerimaan_penjualan_detail', function (Blueprint $table) {
            $table->string('piutang_penjualan_type')->after('piutang_penjualan_id');
        });
    }
};
