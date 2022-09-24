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
        Schema::connection('mysql2')->table('pengeluaran_pembelian', function (Blueprint $table) {
            $table->dropColumn(['akun_kas_id']);
            $table->date('tgl_pengeluaran')->after('kode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('pengeluaran_pembelian', function (Blueprint $table) {
            $table->unsignedBigInteger('akun_kas_id')->after('supplier_id');
            $table->dropColumn('tgl_pengeluaran');
        });
    }
};
