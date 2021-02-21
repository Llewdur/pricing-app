<?php

namespace Database\Factories;

use App\Models\Pricingoption;
use Illuminate\Database\Eloquent\Factories\Factory;

class PricingoptionFactory extends Factory
{
    protected $model = Pricingoption::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
            'priceincents' => rand(100, 10000),
        ];
    }
}
