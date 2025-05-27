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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('minuman_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('discount_amount', 10, 2);
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        Schema::table('discounts', function (Blueprint $table) {
            $table->foreign('minuman_id')
                  ->references('id')
                  ->on('minumans')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
