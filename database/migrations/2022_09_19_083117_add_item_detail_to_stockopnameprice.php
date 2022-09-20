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
        Schema::connection('mysql2')->table('persediaan_stock_opname_price', function (Blueprint $table) {
            $table->string('active_cash')->after('id');
            $table->string('kondisi')->after('tgl_input');
            $table->unsignedBigInteger('gudang_id')->after('kondisi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('persediaan_stock_opname_price', function (Blueprint $table) {
            $table->dropColumn(['active_cash', 'kondisi', 'gudang_id']);
        });
    }
};
