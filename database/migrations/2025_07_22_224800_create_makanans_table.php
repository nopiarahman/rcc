<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('makanans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('deskripsi')->nullable();
            $table->string('short_description')->nullable();
            $table->string('tag')->nullable();
            $table->string('kategori')->nullable();
            $table->integer('base_price');
            $table->boolean('is_habis')->default(false);
            $table->string('foto')->nullable();
            $table->unsignedBigInteger('default_topping')->nullable();
            $table->unsignedBigInteger('default_size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('makanans');
    }
};
