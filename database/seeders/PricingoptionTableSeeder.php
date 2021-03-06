<?php

namespace Database\Seeders;

use App\Models\Pricingoption;
use App\Models\Purchasabletype;
use Illuminate\Database\Seeder;

class PricingoptionTableSeeder extends Seeder
{
    public const DEFAULT_ARRAY = [
        'Standard-class',
        'Premium-class',
        'Goggles',
        'Small-coffee',
    ];

    public function run()
    {
        foreach (self::DEFAULT_ARRAY as $id => $name) {
            $id++;

            Pricingoption::updateOrCreate(
                [
                    'id' => $id,
                ],
                [
                    'id' => $id,
                    'name' => $name,
                    'priceincents' => rand(100, 10000),
                    'purchasabletype_id' => Purchasabletype::inRandomOrder()->firstOrFail()->id,
                ]);
        }
    }
}
