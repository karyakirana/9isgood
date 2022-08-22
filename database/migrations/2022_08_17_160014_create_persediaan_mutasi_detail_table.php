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
        Schema::connection('mysql2')->create('persediaan_mutasi_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('persediaan_mutasi_id');
            $table->unsignedBigInteger('produk_id');
            $table->integer('harga');
            $table->integer('jumlah');
            $table->bigInteger('sub_total');
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
        Schema::connection('mysql2')->dropIfExists('persediaan_mutasi_detail');
    }
};
