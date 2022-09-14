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
        Schema::connection('mysql2')->create('pengeluaran_pembelian', function (Blueprint $table) {
            $table->id();
            $table->string('active_cash');
            $table->string('jenis'); // INTERNAL atau BLU
            $table->string('kode');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('akun_kas_id');
            $table->unsignedBigInteger('user_id');
            $table->bigInteger('total_pengeluaran');
            $table->text('keterangan');
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
        Schema::connection('mysql2')->dropIfExists('pengeluaran_pembelian');
    }
};
