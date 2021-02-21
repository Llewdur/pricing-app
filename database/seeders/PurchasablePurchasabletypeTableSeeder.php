<?php

namespace Database\Seeders;

use App\Models\Purchasabletype;
use Illuminate\Database\Seeder;

class PurchasablePurchasabletypeTableSeeder extends Seeder
{
    public function run()
    {
        Purchasabletype::factory(10)->create();
    }
}
