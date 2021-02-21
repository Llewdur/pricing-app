<?php

namespace Database\Seeders;

use App\Models\Membershiptype;
use Illuminate\Database\Seeder;

class MembershiptypeTableSeeder extends Seeder
{
    public const DEFAULT_ARRAY = [
        'Standard',
        'Premium',
    ];

    public function run()
    {
        foreach (self::DEFAULT_ARRAY as $id => $name) {
            $id++;

            Membershiptype::updateOrCreate(
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
