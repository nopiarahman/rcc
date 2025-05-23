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
        Schema::table('web_settings', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->after('theme')->default(-1.66651);
            $table->decimal('longitude', 11, 8)->after('latitude')->default(103.65238);
            $table->integer('delivery_radius')->after('longitude')->default(600); // in meters
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'delivery_radius']);
        });
    }
};
