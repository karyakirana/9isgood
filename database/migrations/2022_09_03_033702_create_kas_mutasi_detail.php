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
        Schema::connection('mysql2')->create('kas_mutasi_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kas_mutasi_id');
            $table->string('jenis'); // masuk keluar
            $table->unsignedBigInteger('akun_kas_id');
            $table->bigInteger('nominal_masuk')->nullable();
            $table->bigInteger('nominal_keluar')->nullable();
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
        Schema::connection('mysql2')->dropIfExists('kas_mutasi_detail');
    }
};
