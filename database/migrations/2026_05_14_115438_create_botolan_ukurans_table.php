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
        Schema::create('botolan_ukurans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('botolan_id')->constrained('botolans')->onDelete('cascade');
            $table->string('label');
            $table->integer('harga');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('botolan_ukurans');
    }
};
