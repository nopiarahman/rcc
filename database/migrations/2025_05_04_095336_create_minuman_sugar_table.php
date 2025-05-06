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
        Schema::create('minuman_sugar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('minuman_id')->constrained('minumans')->onDelete('cascade');
            $table->foreignId('sugar_id')->constrained('sugars')->onDelete('cascade');
            $table->decimal('extra_price', 10, 2)->nullable(); // jika level gula tertentu lebih mahal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minuman_sugar');
    }
};
