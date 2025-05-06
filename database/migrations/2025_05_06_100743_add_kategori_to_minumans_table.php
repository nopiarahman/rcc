<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('minumans', function (Blueprint $table) {
            $table->string('kategori')->after('nama')->nullable(); // Bisa diubah jadi tidak nullable jika wajib
        });
    }
    
    public function down(): void
    {
        Schema::table('minumans', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
