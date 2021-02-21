<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class Pricing
{
    public Collection $pricingoptions;

    public Pricingoption $pricingoption;

    public Purchasable $purchasable;

    public User $user;

    public Venue $venue;

    public function __construct(Purchasable $purchasable, Venue $venue, User $user)
    {
        $this->purchasable = $purchasable;
        $this->venue = $venue;
        $this->user = $user;
    }

    public function getPricingOptions(): self
    {
        $this->pricingoptions = Pricingoption::whereIn('purchasabletype_id', $this->purchasable->types->pluck('id'))->get();

        return $this;
    }

    public function applyAdjustments(): self
    {
        foreach ($this->pricingoptions as $this->pricingoption) {
            $this->pricingoption->priceincentsAdjusted = $this->pricingoption->priceincents;

            PricingAdjustment::where('membership_type_id', $this->user->membership_type_id)
                ->where('venue_id', $this->venue->id)
                ->where('age_start', '<=', Carbon::today()->diffInYears($this->user->dob))
                ->where('age_end', '>=', Carbon::today()->diffInYears($this->user->dob))
                ->each(function ($pricingAdjustment) {
                    $this->applyAllAdjustmentsMethods($pricingAdjustment);
                });
        }

        return $this;
    }

    public function applyAllAdjustmentsMethods(PricingAdjustment $pricingAdjustment): void
    {
        $this->setPriceincentsAdjustedOffset($pricingAdjustment);
        $this->setPriceincentsAdjustedFixed($pricingAdjustment);
        $this->setPriceincentsAdjustedMultiplier($pricingAdjustment);
    }

    public function getCheapest(): int
    {
        return $this->pricingoption->priceincentsAdjusted;
    }

    public function getAdjustMethodUsed(): string
    {
        return $this->pricingoption->adjust_methodUsed;
    }

    public function setPriceincentsAdjustedOffset(PricingAdjustment $pricingAdjustment): void
    {
        $adjustMethod = 'offset';

        if ($pricingAdjustment->adjust_method !== $adjustMethod) {
            return;
        }

        $priceincentsAdjusted = $this->pricingoption->priceincents + $pricingAdjustment->adjust_value;

        $this->setPriceincentsAdjusted($priceincentsAdjusted, $adjustMethod);
    }

    public function setPriceincentsAdjustedFixed(PricingAdjustment $pricingAdjustment): void
    {
        $adjustMethod = 'fixed';

        if ($pricingAdjustment->adjust_method !== $adjustMethod) {
            return;
        }

        $priceincentsAdjusted = $pricingAdjustment->adjust_value;

        $this->setPriceincentsAdjusted($priceincentsAdjusted, $adjustMethod);
    }

    public function setPriceincentsAdjustedMultiplier(PricingAdjustment $pricingAdjustment): void
    {
        $adjustMethod = 'multiplier';

        if ($pricingAdjustment->adjust_method !== $adjustMethod) {
            return;
        }

        $priceincentsAdjusted = $this->pricingoption->priceincents + ($this->pricingoption->priceincents * $pricingAdjustment->adjust_value / 100);

        $this->setPriceincentsAdjusted($priceincentsAdjusted, $adjustMethod);
    }

    public function setPriceincentsAdjusted(int $priceincentsAdjusted, string $adjustMethod): void
    {
        if ($priceincentsAdjusted < $this->pricingoption->priceincentsAdjusted) {
            $this->pricingoption->priceincentsAdjusted = $priceincentsAdjusted;
            $this->pricingoption->adjust_methodUsed = $adjustMethod;
        }
    }
}
