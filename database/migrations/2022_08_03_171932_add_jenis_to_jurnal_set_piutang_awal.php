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
        Schema::connection('mysql2')->table('jurnal_set_piutang_awal', function (Blueprint $table) {
            $table->string('jenis')->after('active_cash');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql2')->table('jurnal_set_piutang_awal', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};
