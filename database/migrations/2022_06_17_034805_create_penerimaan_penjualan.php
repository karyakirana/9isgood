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
        Schema::connection('mysql2')->create('penerimaan_penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('active_cash');
            $table->string('kode');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('akun_kas_id');
            $table->bigInteger('nominal_kas');
            $table->unsignedBigInteger('akun_piutang_id')->nullable();
            $table->bigInteger('nominal_piutang')->nullable();
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
        Schema::connection('mysql2')->dropIfExists('penerimaan_penjualan');
    }
};
