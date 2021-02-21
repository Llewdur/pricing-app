<?php

namespace App\tests\Feature;

use App\Models\Membershiptype;
use App\Models\Pricing;
use App\Models\PricingAdjustment;
use App\Models\Pricingoption;
use App\Models\Purchasable;
use App\Models\Purchasabletype;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    public const PRICE_IN_CENTS = 400;

    public const CATEGORY_NAME = 'Test-category';

    protected $seed = true;

    public function testCreatePurchasable(): Purchasable
    {
        $purchasableArray = [
            'name' => 'class @ £3',
        ];

        $purchasable = Purchasable::factory()->hasTypes(1, [
            'name' => self::CATEGORY_NAME,
        ])->create($purchasableArray);

        $this->assertDatabaseHas('purchasables', $purchasableArray);

        return $purchasable;
    }

    public function testCreateMembershiptype(): Membershiptype
    {
        $dataArray = [
            'name' => 'some unique type',
        ];

        $membershiptype = Membershiptype::factory()->create($dataArray);

        $this->assertDatabaseHas('membershiptypes', $dataArray);

        return $membershiptype;
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
        $age = rand(15, 30);
        $priceincentsExpected = SELF::PRICE_IN_CENTS + $adjustValue;

        $this->testCreateMembershiptype();
        self::createVenue();
        $membership_type_id = Membershiptype::inRandomOrder()->firstOrFail()->id;

        $purchasable = $this->testCreatePurchasable();
        $pricingOption = $this->createPricingOption($purchasable->types->first->id);
        $user = $this->testCreateUser($age, $membership_type_id);
        $venue = Venue::inRandomOrder()->firstOrFail();

        $cheapest = (new Pricing($purchasable, $venue, $user))
            ->getPricingOptions()
            ->applyAdjustments()
            ->getCheapest();

        $this->assertSame($priceincentsExpected, $cheapest);
    }

    public function testAdjustmentOffset()
    {
        $adjustMethod = 'offset';
        $age = rand(15, 30);
        $adjustValue = -10;
        $priceincentsExpected = SELF::PRICE_IN_CENTS + $adjustValue;

        $this->testCreateMembershiptype();
        self::createVenue();
        $membership_type_id = Membershiptype::inRandomOrder()->firstOrFail()->id;

        $purchasable = $this->testCreatePurchasable();
        $pricingOption = $this->createPricingOption($purchasable->types->first->id);
        $user = $this->testCreateUser($age, $membership_type_id);
        $venue = Venue::inRandomOrder()->firstOrFail();
        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, $adjustValue, $adjustMethod, $pricingOption);

        $cheapest = (new Pricing($purchasable, $venue, $user))
            ->getPricingOptions()
            ->applyAdjustments()
            ->getCheapest();

        $this->assertSame($priceincentsExpected, $cheapest);
    }

    public function testAdjustmentFixed()
    {
        $adjustMethod = 'fixed';
        $age = rand(15, 30);
        $adjustValue = 2;
        $priceincentsExpected = $adjustValue;

        $this->testCreateMembershiptype();
        self::createVenue();
        $membership_type_id = Membershiptype::inRandomOrder()->firstOrFail()->id;

        $purchasable = $this->testCreatePurchasable();
        $pricingOption = $this->createPricingOption($purchasable->types->first->id);
        $user = $this->testCreateUser($age, $membership_type_id);
        $venue = Venue::inRandomOrder()->firstOrFail();
        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, $adjustValue, $adjustMethod, $pricingOption);

        $cheapest = (new Pricing($purchasable, $venue, $user))
            ->getPricingOptions()
            ->applyAdjustments()
            ->getCheapest();

        $this->assertSame($priceincentsExpected, $cheapest);
    }

    public function testAdjustmentMultiplier()
    {
        $adjustMethod = 'multiplier';
        $age = rand(15, 30);
        $adjustValue = -80; // 80% off
        $priceincentsExpected = SELF::PRICE_IN_CENTS + (SELF::PRICE_IN_CENTS * $adjustValue / 100);

        // $this->testCreateMembershiptype();
        // self::createVenue();
        $membership_type_id = Membershiptype::inRandomOrder()->firstOrFail()->id;

        $purchasable = $this->testCreatePurchasable();
        $pricingOption = $this->createPricingOption($purchasable->types->first->id);
        $user = $this->testCreateUser($age, $membership_type_id);
        $venue = Venue::inRandomOrder()->firstOrFail();
        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, $adjustValue, $adjustMethod, $pricingOption);

        $cheapest = (new Pricing($purchasable, $venue, $user))
            ->getPricingOptions()
            ->applyAdjustments()
            ->getCheapest();

        $this->assertSame($priceincentsExpected, $cheapest);
    }

    public function testAdjustWithOffsetAndFixedWhereOffsetIsCheaper()
    {
        $adjustMethod = 'offset';
        $age = rand(15, 30);
        $adjustValue = -20;
        $priceincentsExpected = SELF::PRICE_IN_CENTS + $adjustValue;

        $this->testCreateMembershiptype();
        self::createVenue();
        $membership_type_id = Membershiptype::inRandomOrder()->firstOrFail()->id;

        $purchasable = $this->testCreatePurchasable();
        $pricingOption = $this->createPricingOption($purchasable->types->first->id);
        $user = $this->testCreateUser($age, $membership_type_id);
        $venue = Venue::inRandomOrder()->firstOrFail();
        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, $adjustValue, $adjustMethod, $pricingOption);
        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, 400, 'fixed', $pricingOption);

        $pricing = (new Pricing($purchasable, $venue, $user))
            ->getPricingOptions()
            ->applyAdjustments();

        $this->assertSame($adjustMethod, $pricing->getAdjustMethodUsed());

        $this->assertSame($priceincentsExpected, $pricing->getCheapest());
    }

    public function testAdjustWithOffsetAndFixedWhereFixedIsCheaper()
    {
        $adjustMethod = 'fixed';
        $age = rand(15, 30);
        $adjustValue = 2;
        $priceincentsExpected = $adjustValue;

        $this->testCreateMembershiptype();
        self::createVenue();
        $membership_type_id = Membershiptype::inRandomOrder()->firstOrFail()->id;

        $purchasable = $this->testCreatePurchasable();
        $pricingOption = $this->createPricingOption($purchasable->types->first->id);
        $user = $this->testCreateUser($age, $membership_type_id);
        $venue = Venue::inRandomOrder()->firstOrFail();

        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, $adjustValue, $adjustMethod, $pricingOption);
        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, 50, 'offset', $pricingOption);

        $pricing = (new Pricing($purchasable, $venue, $user))
            ->getPricingOptions()
            ->applyAdjustments();

        $this->assertSame($adjustMethod, $pricing->getAdjustMethodUsed());

        $this->assertSame($priceincentsExpected, $pricing->getCheapest());
    }

    public function testUnderTwentyFiveReduceByTwentyFivePercent()
    {
        $adjustMethod = 'multiplier';
        $age = 20;
        $adjustValue = -25;
        $priceincentsExpected = SELF::PRICE_IN_CENTS + (SELF::PRICE_IN_CENTS * $adjustValue / 100);

        $membership_type_id = Membershiptype::inRandomOrder()->firstOrFail()->id;

        $purchasable = $this->testCreatePurchasable();
        $pricingOption = $this->createPricingOption($purchasable->types->first->id);
        $user = $this->testCreateUser($age, $membership_type_id);
        $venue = Venue::inRandomOrder()->firstOrFail();

        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, $adjustValue, $adjustMethod, $pricingOption);

        $pricing = (new Pricing($purchasable, $venue, $user))
            ->getPricingOptions()
            ->applyAdjustments();

        $this->assertSame($adjustMethod, $pricing->getAdjustMethodUsed());

        $this->assertSame($priceincentsExpected, $pricing->getCheapest());
    }

    public function testCambridgeGymPaysThreePoundsForCoffee()
    {
        $adjustMethod = 'fixed';
        $age = rand(1, 99);
        $adjustValue = 300;
        $priceincentsExpected = $adjustValue;

        $membership_type_id = Membershiptype::inRandomOrder()->firstOrFail()->id;

        $purchasable = $this->testCreatePurchasable();
        $pricingOption = $this->createPricingOption($purchasable->types->first->id);
        $user = $this->testCreateUser($age, $membership_type_id);
        $venue = Venue::factory()->create([
            'name' => 'Not Cambridge Gym',
        ]);

        $cheapest = (new Pricing($purchasable, $venue, $user))
            ->getPricingOptions()
            ->applyAdjustments()
            ->getCheapest();

        $this->assertNotSame($priceincentsExpected, $cheapest);

        $venue = Venue::factory()->create([
            'name' => 'Cambridge Gym',
        ]);

        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, $adjustValue, $adjustMethod, $pricingOption);

        $cheapest = (new Pricing($purchasable, $venue, $user))
            ->getPricingOptions()
            ->applyAdjustments()
            ->getCheapest();

        $this->assertSame($priceincentsExpected, $cheapest);
    }

    public function testPremiumGetsCoffeeForFree()
    {
        $adjustMethod = 'fixed';
        $age = rand(1, 99);
        $adjustValue = 0;
        $priceincentsExpected = $adjustValue;

        $membership_type_id = Membershiptype::inRandomOrder()->firstOrFail()->id;

        $purchasable = $this->testCreatePurchasable();
        $pricingOption = $this->createPricingOption($purchasable->types->first->id);
        $user = $this->testCreateUser($age, $membership_type_id);
        $venue = Venue::inRandomOrder()->firstOrFail();

        $cheapest = (new Pricing($purchasable, $venue, $user))
            ->getPricingOptions()
            ->applyAdjustments()
            ->getCheapest();

        $this->assertNotSame($priceincentsExpected, $cheapest);

        $this->createPricingAdjustment($membership_type_id, $venue->id, $age, $adjustValue, $adjustMethod, $pricingOption);

        $cheapest = (new Pricing($purchasable, $venue, $user))
            ->getPricingOptions()
            ->applyAdjustments()
            ->getCheapest();

        $this->assertSame($priceincentsExpected, $cheapest);
    }

    private function createPricingOption(Purchasabletype $purchasableCategory): Pricingoption
    {
        $pricingOptionArray = [
            'name' => 'Some class @ £' . SELF::PRICE_IN_CENTS / 100,
            'priceincents' => SELF::PRICE_IN_CENTS,
            'purchasabletype_id' => $purchasableCategory->id,
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

    private static function createVenue()
    {
        Venue::factory()->create();
    }
}
