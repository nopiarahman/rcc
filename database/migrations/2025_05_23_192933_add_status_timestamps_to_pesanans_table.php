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
        Schema::table('pesanans', function (Blueprint $table) {
            $table->timestamp('waktu_diproses')->nullable()->after('status');
            $table->timestamp('waktu_diantar')->nullable()->after('waktu_diproses');
            $table->timestamp('waktu_selesai')->nullable()->after('waktu_diantar');
            $table->timestamp('waktu_dibatalkan')->nullable()->after('waktu_selesai');
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
