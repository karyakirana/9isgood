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
        Schema::connection('mysql2')->create('persediaan_opname_koreksi', function (Blueprint $table) {
            $table->id();
            $table->string('active_cash');
            $table->unsignedBigInteger('stock_opname_koreksi_id');
            $table->string('jenis');
            $table->string('kondisi');
            $table->unsignedBigInteger('gudang_id');
            $table->unsignedBigInteger('user_id');
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
        Schema::connection('mysql2')->dropIfExists('persediaan_opname_koreksi');
    }
};
