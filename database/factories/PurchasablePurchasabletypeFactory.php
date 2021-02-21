<?php

namespace Database\Factories;

use App\Models\Purchasable;
use App\Models\PurchasablePurchasabletype;
use App\Models\Purchasabletype;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchasablePurchasabletypeFactory extends Factory
{
    protected $model = PurchasablePurchasabletype::class;

    public function definition()
    {
        return [
            'purchasable_id' => Purchasable::inRandomOrder()->firstOrFail()->id,
            'purchasabletype_id' => Purchasabletype::inRandomOrder()->firstOrFail()->id,
        ];
    }
}
