<?php

namespace Database\Seeders;

use App\Models\Purchasabletype;
use Illuminate\Database\Seeder;

class PurchasabletypeTableSeeder extends Seeder
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

            Purchasabletype::updateOrCreate(
                [
                    'id' => $id,
                ],
                [
                    'id' => $id,
                    'name' => $name,
                ]);
        }
    }
}
