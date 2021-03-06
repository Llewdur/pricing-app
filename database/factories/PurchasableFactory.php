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
            'name' => Str::random(40),
        ];
    }
}
