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
        Schema::create('pesanan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained()->onDelete('cascade');
            $table->foreignId('minuman_id')->constrained('minumans')->onDelete('cascade');
            $table->string('nama_minuman');
            $table->integer('harga');
            $table->integer('qty');
            $table->string('size')->nullable();
            $table->string('gula')->nullable();
            $table->string('topping')->nullable();
            $table->text('catatan')->nullable();
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan_details');
    }
};
