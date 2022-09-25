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
        Schema::connection('mysql2')->table('kas_mutasi', function (Blueprint $table) {
            $table->date('tgl_mutasi')->after('kode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('kas_mutasi', function (Blueprint $table) {
            $table->dropColumn('tgl_mutasi');
        });
    }
};