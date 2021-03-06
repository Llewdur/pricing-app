<?php

namespace Database\Factories;

use App\Models\Pricingoption;
use App\Models\Purchasabletype;
use Illuminate\Database\Eloquent\Factories\Factory;

class PricingoptionFactory extends Factory
{
    protected $model = Pricingoption::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
            'priceincents' => rand(100, 10000),
            'purchasabletype_id' => Purchasabletype::inRandomOrder()->firstOrFail()->id,
        ];
    }
}
