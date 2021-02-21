<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            MembershiptypeTableSeeder::class,
            PurchasableTableSeeder::class,
            VenueTableSeeder::class,
            PricingoptionTableSeeder::class,
            // PricingAdjustmentTableSeeder::class,
            UserTableSeeder::class,
        ]);
    }
}
