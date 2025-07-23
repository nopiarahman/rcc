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
        // First, drop the foreign key constraint to modify the column
        Schema::table('pesanan_details', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['minuman_id']);
            
            // Make minuman_id nullable
            $table->unsignedBigInteger('minuman_id')->nullable()->change();
            
            // Make nama_minuman nullable
            $table->string('nama_minuman')->nullable()->change();
            
            // Add makanan_id column
            $table->unsignedBigInteger('makanan_id')->nullable()->after('minuman_id');
            $table->foreign('makanan_id')->references('id')->on('makanans')->onDelete('cascade');
            
            // Add nama_makanan column
            $table->string('nama_makanan')->nullable()->after('nama_minuman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan_details', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['makanan_id']);
            
            // Drop columns
            $table->dropColumn('makanan_id');
            $table->dropColumn('nama_makanan');
            
            // Revert minuman_id to not nullable
            $table->unsignedBigInteger('minuman_id')->nullable(false)->change();
            
            // Revert nama_minuman to not nullable
            $table->string('nama_minuman')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('minuman_id')->references('id')->on('minumans')->onDelete('cascade');
        });
    }
};