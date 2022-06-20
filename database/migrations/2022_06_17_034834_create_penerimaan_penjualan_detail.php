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
        Schema::connection('mysql2')->create('penerimaan_penjualan_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penerimaan_penjualan_id');
            $table->unsignedBigInteger('piutang_penjualan_id');
            $table->string('piutang_penjualan_type');
            $table->bigInteger('nominal_dibayar');
            $table->bigInteger('kurang_bayar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->dropIfExists('penerimaan_penjualan_detail');
    }
};
