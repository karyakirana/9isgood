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
        Schema::connection('mysql2')->table('persediaan_transaksi_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('persediaan_id')->nullable()->after('persediaan_transaksi_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('persediaan_transaksi_detail', function (Blueprint $table) {
            $table->dropColumn('persediaan_id');
        });
    }
};
