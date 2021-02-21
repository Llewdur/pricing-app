<?php

namespace App\tests\Feature;

use App\Models\Membershiptype;
use App\Models\Pricing;
use App\Models\PricingAdjustment;
use App\Models\Pricingoption;
use App\Models\Purchasable;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    public const PRICE_IN_CENTS = 300;

    protected $seed = true;

    public function testCreatePurchasable(): Purchasable
    {
        $purchasableArray = [
            'name' => 'class @ £3',
            'priceincents' => SELF::PRICE_IN_CENTS,
            'type' => 'class',
        ];

        $purchasable = Purchasable::factory()->create($purchasableArray);

        $this->assertDatabaseHas('purchasables', $purchasableArray);

        return $purchasable;
    }

    public function testCreateUser(int $age = null, int $membership_type_id = null): User
    {
        $age = $age ?? rand(15, 30);
        $membership_type_id = $membership_type_id ?? Membershiptype::inRandomOrder()->firstOrFail()->id;

        $userArray = [
            'dob' => Carbon::today()->subYears($age)->format('Y-m-d'),
            'membership_type_id' => $membership_type_id,
        ];

        $user = User::factory()->create($userArray);

        $this->assertDatabaseHas('users', $userArray);

        return $user;
    }

    public function testNoAdjustment()
    {
        $adjustValue = 0;
        $priceincentsExpected = SELF::PRICE_IN_CENTS + $adjustValue;

        $purchasable = $this->testCreatePurchasable();
        $user = $this->testCreateUser();
        $venue = Venue::inRandomOrder()->firstOrFail();

        $priceincentsUser = (new Pricing($user))
            ->addPurchasable($purchasable)
            ->getPricingAdjustments($venue->id)
            ->getCheapest();

        $this->assertSame($priceincentsExpected, $priceincentsUser);
    }

    public function testAdjustmentOffset()
    {
        $adjustMethod = 'offset';
        $age = rand(15, 30);
        $adjustValue = -10;
        $priceincentsExpected = SELF::PRICE_IN_CENTS + $adjustValue;

        $membership_type_id = Membershiptype::inRandomOrder()->firstOrFail()->id;

        $pricingOption = $this->createPricingOption();
        $purchasable = $this->testCreatePurchasable();
        $user = $this->testCreateUser($age, $membership_type_id);
        $venue = Venue::inRandomOrder()->firstOrFail();
        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, $adjustValue, $adjustMethod, $pricingOption);

        $priceincentsUser = (new Pricing($user))
            ->addPurchasable($purchasable)
            ->getPricingAdjustments($venue->id)
            ->getCheapest();

        $this->assertSame($priceincentsExpected, $priceincentsUser);
    }

    public function testAdjustmentFixed()
    {
        $adjustMethod = 'fixed';
        $age = rand(15, 30);
        $adjustValue = 2;
        $priceincentsExpected = $adjustValue;

        $membership_type_id = Membershiptype::inRandomOrder()->firstOrFail()->id;

        $pricingOption = $this->createPricingOption();
        $purchasable = $this->testCreatePurchasable();
        $user = $this->testCreateUser($age, $membership_type_id);
        $venue = Venue::inRandomOrder()->firstOrFail();

        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, $adjustValue, $adjustMethod, $pricingOption);

        $priceincentsUser = (new Pricing($user))
            ->addPurchasable($purchasable)
            ->getPricingAdjustments($venue->id)
            ->getCheapest();

        $this->assertSame($priceincentsExpected, $priceincentsUser);
    }

    public function testAdjustmentMultiplier()
    {
        $adjustMethod = 'multiplier';
        $age = rand(15, 30);
        $adjustValue = 80; // 80%
        $priceincentsExpected = SELF::PRICE_IN_CENTS * $adjustValue / 100;

        $membership_type_id = Membershiptype::inRandomOrder()->firstOrFail()->id;

        $pricingOption = $this->createPricingOption();
        $purchasable = $this->testCreatePurchasable();
        $user = $this->testCreateUser($age, $membership_type_id);
        $venue = Venue::inRandomOrder()->firstOrFail();

        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, $adjustValue, $adjustMethod, $pricingOption);

        $priceincentsUser = (new Pricing($user))
            ->addPurchasable($purchasable)
            ->getPricingAdjustments($venue->id)
            ->getCheapest();

        $this->assertSame($priceincentsExpected, $priceincentsUser);
    }

    public function testAdjustWithOffsetAndFixedWhereOffsetIsCheaper()
    {
        $adjustMethod = 'offset';
        $age = rand(15, 30);
        $adjustValue = -20;
        $priceincentsExpected = SELF::PRICE_IN_CENTS + $adjustValue;

        $membership_type_id = Membershiptype::inRandomOrder()->firstOrFail()->id;

        $pricingOption = $this->createPricingOption();
        $purchasable = $this->testCreatePurchasable();
        $user = $this->testCreateUser($age, $membership_type_id);
        $venue = Venue::inRandomOrder()->firstOrFail();

        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, $adjustValue, $adjustMethod, $pricingOption);
        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, 400, 'fixed', $pricingOption);

        $pricing = (new Pricing($user))
            ->addPurchasable($purchasable)
            ->getPricingAdjustments($venue->id);

        $this->assertSame($adjustMethod, $pricing->getAdjustMethodUsed());

        $this->assertSame($priceincentsExpected, $pricing->getCheapest());
    }

    public function testAdjustWithOffsetAndFixedWhereFixedIsCheaper()
    {
        $adjustMethod = 'fixed';
        $age = rand(15, 30);
        $adjustValue = 2;
        $priceincentsExpected = $adjustValue;

        $membership_type_id = Membershiptype::inRandomOrder()->firstOrFail()->id;

        $pricingOption = $this->createPricingOption();
        $purchasable = $this->testCreatePurchasable();
        $user = $this->testCreateUser($age, $membership_type_id);
        $venue = Venue::inRandomOrder()->firstOrFail();

        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, $adjustValue, $adjustMethod, $pricingOption);
        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, 50, 'offset', $pricingOption);

        $pricing = (new Pricing($user))
            ->addPurchasable($purchasable)
            ->getPricingAdjustments($venue->id);

        $this->assertSame($adjustMethod, $pricing->getAdjustMethodUsed());

        $this->assertSame($priceincentsExpected, $pricing->getCheapest());
    }

    private function createPricingOption(): Pricingoption
    {
        $pricingOptionArray = [
            'name' => 'Some class @ £' . SELF::PRICE_IN_CENTS / 100,
            'priceincents' => SELF::PRICE_IN_CENTS,
        ];

        return Pricingoption::factory()->create($pricingOptionArray);
    }

    private function createPricingAdjustment(int $membership_type_id, int $venue_id, int $age, int $adjustValue, string $adjustMethod, PricingOption $pricingOption)
    {
        $pricingAdjustmentArray = [
            'membership_type_id' => $membership_type_id,
            'venue_id' => $venue_id,
            'age_start' => $age,
            'age_end' => $age,
            'adjust_value' => $adjustValue,
            'adjust_method' => $adjustMethod,
            'pricing_option_id' => $pricingOption->id,
        ];

        PricingAdjustment::factory()->create($pricingAdjustmentArray);

        $this->assertDatabaseHas('pricing_adjustments', $pricingAdjustmentArray);
    }
}
