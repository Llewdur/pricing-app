<?php

namespace Tests\Unit;

use App\Models\Purchasable;
use PHPUnit\Framework\TestCase;

class PricingAdjustmentTest extends TestCase
{
    public function testBasicTest()
    {
        $purchasableArray = [
            'name' => 'class @ £3',
            'priceincents' => 300,
            'type' => 'class',
        ];

        Purchasable::factory()->make($purchasableArray);

        // $this->assertDatabaseHas('purchasables', $purchasableArray);
    }
}
