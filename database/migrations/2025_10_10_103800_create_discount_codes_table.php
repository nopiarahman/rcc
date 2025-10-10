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
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode unik untuk discount
            $table->string('name'); // Nama diskon untuk admin
            $table->text('description')->nullable(); // Deskripsi diskon
            $table->enum('discount_type', ['percentage', 'fixed']); // Tipe diskon: persentase atau nominal tetap
            $table->decimal('discount_amount', 10, 2); // Jumlah diskon
            $table->decimal('minimum_purchase', 10, 2)->default(0); // Minimum pembelian untuk bisa menggunakan kode
            $table->integer('max_redeem')->nullable(); // Maksimal penggunaan kode (null = unlimited)
            $table->integer('used_count')->default(0); // Jumlah penggunaan yang sudah terpakai
            $table->dateTime('start_date'); // Tanggal mulai berlaku
            $table->dateTime('end_date'); // Tanggal berakhir
            $table->boolean('is_active')->default(true); // Status aktif/non-aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
    }
};
