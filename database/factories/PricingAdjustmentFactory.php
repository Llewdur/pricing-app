<?php

namespace Database\Factories;

use App\Models\Membershiptype;
use App\Models\PricingAdjustment;
use App\Models\Pricingoption;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

class PricingAdjustmentFactory extends Factory
{
    protected $model = PricingAdjustment::class;

    public function definition()
    {
        return [
            'membership_type_id' => Membershiptype::inRandomOrder()->firstOrFail()->id,
            'venue_id' => Venue::inRandomOrder()->firstOrFail()->id,
            'age_start' => rand(1, 10),
            'age_end' => rand(20, 30),
            'adjust_method' => array_rand(array_flip(['fixed', 'multiplier', 'offset'])),
            'adjust_value' => rand(1, 100),
            'pricing_option_id' => Pricingoption::inRandomOrder()->firstOrFail()->id,
        ];
    }
}
