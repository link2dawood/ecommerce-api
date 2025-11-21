<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    public function run()
    {
        Coupon::create([
            'code' => 'WELCOME10',
            'type' => 'percentage',
            'value' => 10,
            'min_purchase' => 50,
            'usage_limit' => 100,
            'valid_from' => Carbon::now(),
            'valid_until' => Carbon::now()->addMonths(3),
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'SAVE20',
            'type' => 'fixed',
            'value' => 20,
            'min_purchase' => 100,
            'usage_limit' => 50,
            'valid_from' => Carbon::now(),
            'valid_until' => Carbon::now()->addMonths(2),
            'is_active' => true,
        ]);
    }
}