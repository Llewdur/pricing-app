<?php

namespace Database\Seeders;

use App\Models\PricingAdjustment;
use Illuminate\Database\Seeder;

class PricingAdjustmentTableSeeder extends Seeder
{
    public function run()
    {
        PricingAdjustment::factory(10)->create();
    }
}
