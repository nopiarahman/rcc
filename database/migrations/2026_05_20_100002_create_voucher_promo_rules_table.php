<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_promo_rules', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->unsignedBigInteger('min_purchase_amount');
            $table->string('voucher_discount_type'); // 'fixed' or 'percentage'
            $table->decimal('voucher_discount_value', 10, 2);
            $table->unsignedBigInteger('voucher_min_purchase')->default(0);
            $table->unsignedInteger('voucher_validity_days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_promo_rules');
    }
};
