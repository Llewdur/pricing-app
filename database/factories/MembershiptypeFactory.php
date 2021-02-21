<?php

namespace Database\Factories;

use App\Models\Membershiptype;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershiptypeFactory extends Factory
{
    protected $model = Membershiptype::class;

    public function definition()
    {
        return [
            'name' => array_rand(array_flip(['class', 'product'])),
        ];
    }
}
