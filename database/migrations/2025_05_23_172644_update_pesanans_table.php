<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // First, drop the foreign key constraint if it exists
        if (Schema::hasTable('pesanans')) {
            // Add new columns
            Schema::table('pesanans', function (Blueprint $table) {
                if (!Schema::hasColumn('pesanans', 'nomor_pesanan')) {
                    $table->string('nomor_pesanan')->unique()->after('id');
                }
                if (!Schema::hasColumn('pesanans', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('nomor_pesanan')->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('pesanans', 'nomor_telepon')) {
                    $table->string('nomor_telepon')->after('nama_pemesan');
                }
                if (!Schema::hasColumn('pesanans', 'catatan')) {
                    $table->text('catatan')->nullable()->after('waktu_pengantaran');
                }
                if (!Schema::hasColumn('pesanans', 'total_harga')) {
                    $table->decimal('total_harga', 15, 2)->after('total');
                }
                
                // Update status column if it exists
                if (Schema::hasColumn('pesanans', 'status')) {
                    // Convert existing status values to new format
                    \DB::statement("ALTER TABLE pesanans MODIFY COLUMN status ENUM('pending', 'diproses', 'selesai', 'menunggu_konfirmasi', 'dikirim', 'dibatalkan') DEFAULT 'menunggu_konfirmasi'");
                    \DB::statement("UPDATE pesanans SET status = 'menunggu_konfirmasi' WHERE status = 'pending'");
                }
                
                // Modify existing columns if needed
                if (Schema::hasColumn('pesanans', 'alamat_pengantaran')) {
                    $table->text('alamat_pengantaran')->change();
                }
                
                // Drop the items column if it exists and we're using pesanan_details table
                if (Schema::hasColumn('pesanans', 'items')) {
                    $table->dropColumn('items');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasTable('pesanans')) {
            Schema::table('pesanans', function (Blueprint $table) {
                // Drop added columns if they exist
                $columnsToDrop = [];
                
                if (Schema::hasColumn('pesanans', 'nomor_pesanan')) {
                    $columnsToDrop[] = 'nomor_pesanan';
                }
                if (Schema::hasColumn('pesanans', 'user_id')) {
                    $columnsToDrop[] = 'user_id';
                }
                if (Schema::hasColumn('pesanans', 'nomor_telepon')) {
                    $columnsToDrop[] = 'nomor_telepon';
                }
                if (Schema::hasColumn('pesanans', 'catatan')) {
                    $columnsToDrop[] = 'catatan';
                }
                if (Schema::hasColumn('pesanans', 'total_harga')) {
                    $columnsToDrop[] = 'total_harga';
                }
                
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
                
                // Revert status enum if column exists
                if (Schema::hasColumn('pesanans', 'status')) {
                    \DB::statement("ALTER TABLE pesanans MODIFY COLUMN status ENUM('pending', 'diproses', 'selesai') DEFAULT 'pending'");
                    \DB::statement("UPDATE pesanans SET status = 'pending' WHERE status = 'menunggu_konfirmasi'");
                }
                
                // Add back items column if it doesn't exist
                if (!Schema::hasColumn('pesanans', 'items')) {
                    $table->json('items')->nullable()->after('waktu_pengantaran');
                }
            });
        }
    }
};
