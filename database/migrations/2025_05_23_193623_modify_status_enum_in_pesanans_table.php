<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyStatusEnumInPesanansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->enum('status', [
                'menunggu_konfirmasi',
                'diproses',
                'diantar',
                'dikirim',
                'selesai',
                'dibatalkan'
            ])->default('menunggu_konfirmasi')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            //
        });
    }
};
