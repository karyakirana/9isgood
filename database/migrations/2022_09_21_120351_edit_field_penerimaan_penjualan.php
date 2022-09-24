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
        Schema::connection('mysql2')->table('penerimaan_penjualan', function (Blueprint $table) {
            $table->dropColumn(['akun_kas_id', 'nominal_kas', 'akun_piutang_id', 'nominal_piutang']);
            $table->unsignedBigInteger('user_id')->after('customer_id');
            $table->bigInteger('total_penerimaan')->after('user_id');
            $table->text('keterangan')->after('total_penerimaan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('penerimaan_penjualan', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'total_penerimaan', 'keterangan']);
            $table->unsignedBigInteger('akun_kas_id')->after('customer_id');
            $table->bigInteger('nominal_kas_id')->after('akun_kas_id');
            $table->unsignedBigInteger('akun_piutang_id')->after('nominal_kas_id');
            $table->bigInteger('nominal_piutang')->after('akun_piutang_id');
        });
    }
};
