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
        Schema::connection('mysql2')->table('persediaan', function (Blueprint $table) {
            $table->dropColumn(['stock_opname', 'stock_akhir', 'stock_lost']);
            $table->bigInteger('saldo')->default(0)->after('stock_keluar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('persediaan', function (Blueprint $table) {
            $table->bigInteger('stock_opname')->nullable()->default(0)->after('harga');
            $table->bigInteger('stock_akhir')->nullable()->default(0)->after('stock_keluar');
            $table->bigInteger('stock_lost')->nullable()->default(0)->after('stock_akhir');
            $table->dropColumn('saldo');
        });
    }
};
