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
        Schema::table('minumans', function (Blueprint $table) {
            $table->foreignId('default_size_id')->nullable()->constrained('sizes');
            $table->foreignId('default_sugar_id')->nullable()->constrained('sugars');
            $table->foreignId('default_topping_id')->nullable()->constrained('toppings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('minumans', function (Blueprint $table) {
            //
        });
    }
};
