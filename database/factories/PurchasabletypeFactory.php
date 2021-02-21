<?php

namespace Database\Factories;

use App\Models\Purchasabletype;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PurchasabletypeFactory extends Factory
{
    protected $model = Purchasabletype::class;

    public function definition()
    {
        return [
            'name' => Str::random(40),
        ];
    }
}
