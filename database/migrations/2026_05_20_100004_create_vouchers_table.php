<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('voucher_promo_rule_id')->nullable()
                ->constrained('voucher_promo_rules')->nullOnDelete();
            $table->string('code', 12)->unique();
            $table->string('discount_type'); // 'fixed' or 'percentage'
            $table->decimal('discount_value', 10, 2);
            $table->unsignedBigInteger('min_purchase')->default(0);
            $table->string('description')->nullable();
            $table->unsignedBigInteger('source_pesanan_id')->nullable(); // order that generated this
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->unsignedBigInteger('used_in_pesanan_id')->nullable(); // order that used this
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
