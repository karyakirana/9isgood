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
        Schema::connection('mysql2')->table('piutang_penjualan', function (Blueprint $table) {
            $table->string('penjualan_type')->after('jurnal_set_piutang_awal_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('piutang_penjualan', function (Blueprint $table) {
            $table->dropColumn('penjualan_type');
        });
    }
};
