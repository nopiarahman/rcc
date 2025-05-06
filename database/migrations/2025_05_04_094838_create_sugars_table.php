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
        Schema::create('sugars', function (Blueprint $table) {
            $table->id();
            $table->string('level');  // Tingkat gula (misalnya: Normal, Less, No Sugar)
            $table->decimal('price', 10, 2);  // Harga tambahan untuk gula
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sugars');
    }
};
