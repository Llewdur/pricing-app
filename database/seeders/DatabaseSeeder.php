<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            MembershiptypeTableSeeder::class,
            PurchasabletypeTableSeeder::class,
            PurchasableTableSeeder::class,
            PurchasablePurchasabletypeTableSeeder::class,
            VenueTableSeeder::class,
            PricingoptionTableSeeder::class,
            // PricingAdjustmentTableSeeder::class,
            UserTableSeeder::class,
        ]);
    }
}
