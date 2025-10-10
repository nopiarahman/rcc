<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiscountCode;
use Illuminate\Support\Facades\DB;

class DiscountCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing discount codes
        DB::table('discount_codes')->delete();
        
        $discountCodes = [
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount 10%',
                'description' => 'Special discount for new customers',
                'discount_type' => 'percentage',
                'discount_amount' => 10,
                'minimum_purchase' => 50000,
                'max_redeem' => 100,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'code' => 'RAMADAN2025',
                'name' => 'Ramadan Special',
                'description' => 'Special discount for Ramadan month',
                'discount_type' => 'percentage',
                'discount_amount' => 15,
                'minimum_purchase' => 100000,
                'max_redeem' => 500,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(20),
                'is_active' => true,
            ],
            [
                'code' => 'WEEKEND25',
                'name' => 'Weekend Special',
                'description' => 'Special discount for weekend orders',
                'discount_type' => 'percentage',
                'discount_amount' => 25,
                'minimum_purchase' => 75000,
                'max_redeem' => null, // Unlimited
                'start_date' => now(),
                'end_date' => now()->addDays(14),
                'is_active' => true,
            ],
            [
                'code' => 'FIXED5000',
                'name' => 'Fixed Rp 5.000 Off',
                'description' => 'Get Rp 5.000 off your order',
                'discount_type' => 'fixed',
                'discount_amount' => 5000,
                'minimum_purchase' => 25000,
                'max_redeem' => 200,
                'start_date' => now(),
                'end_date' => now()->addDays(21),
                'is_active' => true,
            ],
            [
                'code' => 'FIRSTORDER',
                'name' => 'First Order Discount',
                'description' => 'Special discount for first time customers',
                'discount_type' => 'percentage',
                'discount_amount' => 20,
                'minimum_purchase' => 30000,
                'max_redeem' => 50,
                'start_date' => now(),
                'end_date' => now()->addMonths(2),
                'is_active' => true,
            ],
            [
                'code' => 'EXPIRED2024',
                'name' => 'Expired Discount (Test)',
                'description' => 'This discount has expired',
                'discount_type' => 'percentage',
                'discount_amount' => 30,
                'minimum_purchase' => 0,
                'max_redeem' => 100,
                'start_date' => now()->subDays(30),
                'end_date' => now()->subDays(1),
                'is_active' => true,
            ],
            [
                'code' => 'INACTIVE',
                'name' => 'Inactive Discount (Test)',
                'description' => 'This discount is inactive',
                'discount_type' => 'percentage',
                'discount_amount' => 12,
                'minimum_purchase' => 40000,
                'max_redeem' => 75,
                'start_date' => now(),
                'end_date' => now()->addDays(15),
                'is_active' => false,
            ],
        ];
        
        foreach ($discountCodes as $discountCode) {
            DiscountCode::create($discountCode);
        }
        
        $this->command->info('Discount codes created successfully!');
    }
}
