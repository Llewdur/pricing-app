<?php

namespace Database\Factories;

use App\Models\Purchasable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PurchasableFactory extends Factory
{
    protected $model = Purchasable::class;

    public function definition()
    {
        return [
            // 'name' => $this->faker->unique()->word,
            'name' => Str::random(40),
            'priceincents' => rand(100, 10000),
            'type' => array_rand(array_flip(['class', 'product'])),
        ];
    }
}
