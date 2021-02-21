<?php

namespace App\Models;

use Carbon\Carbon;

class Pricing
{
    public Purchasable $purchasable;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function addPurchasable(Purchasable $purchasable): self
    {
        $this->purchasable = $purchasable;

        return $this;
    }

    public function getPricingAdjustments(int $venue_id): self
    {
        $this->purchasable->priceincentsAdjusted = $this->purchasable->priceincents;

        PricingAdjustment::where('membership_type_id', $this->user->membership_type_id)
            ->where('venue_id', $venue_id)
            ->where('age_start', '<=', Carbon::today()->diffInYears($this->user->dob))
            ->where('age_end', '>=', Carbon::today()->diffInYears($this->user->dob))
            ->each(function ($pricingAdjustment) {
                $this->runAllAdjustmentMethods($pricingAdjustment);
            });

        return $this;
    }

    public function runAllAdjustmentMethods(PricingAdjustment $pricingAdjustment): void
    {
        $this->setPriceincentsAdjustedOffset($pricingAdjustment);
        $this->setPriceincentsAdjustedFixed($pricingAdjustment);
        $this->setPriceincentsAdjustedMultiplier($pricingAdjustment);
    }

    public function getCheapest(): int
    {
        return $this->purchasable->priceincentsAdjusted;
    }

    public function getAdjustMethodUsed(): string
    {
        return $this->purchasable->adjust_methodUsed;
    }

    public function setPriceincentsAdjustedOffset(PricingAdjustment $pricingAdjustment): void
    {
        $adjustMethod = 'offset';

        if ($pricingAdjustment->adjust_method !== $adjustMethod) {
            return;
        }

        $priceincentsAdjusted = $this->purchasable->priceincents + $pricingAdjustment->adjust_value;

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

        $priceincentsAdjusted = $this->purchasable->priceincents * $pricingAdjustment->adjust_value / 100;

        $this->setPriceincentsAdjusted($priceincentsAdjusted, $adjustMethod);
    }

    public function setPriceincentsAdjusted(int $priceincentsAdjusted, string $adjustMethod): void
    {
        // $this->purchasable->priceincentsAdjusted = min($this->purchasable->priceincentsAdjusted, $priceincentsAdjusted);

        if ($priceincentsAdjusted < $this->purchasable->priceincentsAdjusted) {
            $this->purchasable->priceincentsAdjusted = $priceincentsAdjusted;
            $this->purchasable->adjust_methodUsed = $adjustMethod;
        }
    }
}
