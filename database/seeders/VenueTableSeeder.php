<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueTableSeeder extends Seeder
{
    public const DEFAULT_ARRAY = [
        'London',
        'Manchester',
        'Kent',
        'Liverpool',
        'East Side',
        'West Side',
        'East London',
    ];

    public function run()
    {
        foreach (self::DEFAULT_ARRAY as $id => $name) {
            $id++;

            Venue::updateOrCreate(
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
