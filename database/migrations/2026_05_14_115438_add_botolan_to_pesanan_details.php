<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pesanan_details', function (Blueprint $table) {
            $table->unsignedBigInteger('botolan_id')->nullable()->after('makanan_id');
            $table->foreign('botolan_id')->references('id')->on('botolans')->onDelete('set null');
            $table->string('nama_botolan')->nullable()->after('nama_makanan');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan_details', function (Blueprint $table) {
            $table->dropForeign(['botolan_id']);
            $table->dropColumn(['botolan_id', 'nama_botolan']);
        });
    }
};
