<?php

namespace Database\Seeders;

use App\Models\Pricingoption;
use Illuminate\Database\Seeder;

class PricingoptionTableSeeder extends Seeder
{
    public const DEFAULT_ARRAY = [
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
                ]);
        }
    }
}
