<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop FK from pesanan_details → botolans first
        Schema::table('pesanan_details', function (Blueprint $table) {
            if (Schema::hasColumn('pesanan_details', 'botolan_id')) {
                $table->dropForeign(['botolan_id']);
                $table->dropColumn('botolan_id');
            }
        });

        // 2. Drop old botolan tables (ukurans first, has FK to botolans)
        Schema::dropIfExists('botolan_ukurans');
        Schema::dropIfExists('botolans');

        // 3. Create botolan_produks: one record per minuman that can be bottled
        Schema::create('botolan_produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('minuman_id')->constrained('minumans')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Create botolan_ukurans: custom sizes/prices per produk
        Schema::create('botolan_ukurans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('botolan_produk_id')->constrained('botolan_produks')->cascadeOnDelete();
            $table->string('label');      // e.g. "250 ml", "500 ml", "1 Liter"
            $table->integer('harga');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Add new botolan FKs to pesanan_details
        Schema::table('pesanan_details', function (Blueprint $table) {
            $table->unsignedBigInteger('botolan_produk_id')->nullable()->after('makanan_id');
            $table->unsignedBigInteger('botolan_ukuran_id')->nullable()->after('botolan_produk_id');
            $table->foreign('botolan_produk_id')->references('id')->on('botolan_produks')->nullOnDelete();
            $table->foreign('botolan_ukuran_id')->references('id')->on('botolan_ukurans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pesanan_details', function (Blueprint $table) {
            $table->dropForeign(['botolan_produk_id']);
            $table->dropForeign(['botolan_ukuran_id']);
            $table->dropColumn(['botolan_produk_id', 'botolan_ukuran_id']);
        });
        Schema::dropIfExists('botolan_ukurans');
        Schema::dropIfExists('botolan_produks');
    }
};
