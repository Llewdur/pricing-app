<?php

namespace Database\Seeders;

use App\Models\Purchasable;
use Illuminate\Database\Seeder;

class PurchasableTableSeeder extends Seeder
{
    public function run()
    {
        Purchasable::factory(10)->hasTypes()->create();
    }
}
