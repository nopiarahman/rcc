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
            $table->time('opening_time')->default('08:00:00')->after('whatsapp_number');
            $table->time('closing_time')->default('22:00:00')->after('opening_time');
            $table->boolean('is_temporarily_closed')->default(false)->after('closing_time');
            $table->text('temporary_closure_message')->nullable()->after('is_temporarily_closed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn([
                'opening_time',
                'closing_time',
                'is_temporarily_closed',
                'temporary_closure_message'
            ]);
        });
    }
};
