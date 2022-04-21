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
        Schema::connection('mysql2')->create('penjualan_piutang_retur', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('saldo_piutang_penjualan_retur_id');
            $table->unsignedBigInteger('jurnal_set_retur_penjualan_id')->nullable();
            $table->unsignedBigInteger('penjualan_retur_id');
            $table->enum('status_bayar', ['lunas', 'belum', 'kurang']);
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
        Schema::connection('mysql2')->dropIfExists('penjualan_piutang_retur');
    }
};
