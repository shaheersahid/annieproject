<?php

namespace App\Services;

use App\Contracts\DealCalculationServiceInterface;
use Carbon\Carbon;

class DealCalculationService implements DealCalculationServiceInterface
{
    public function applyDeal(
        float $amount,
        ?string $dealType,
        ?float $dealValue,
        ?Carbon $startAt,
        ?Carbon $endAt,
        ?Carbon $at = null
    ): float {
        if (! $this->isDealActive($startAt, $endAt, $at) || ! $dealType || $dealValue === null) {
            return $amount;
        }

        if ($dealType === 'fixed') {
            return max(0, $amount - $dealValue);
        }

        if ($dealType === 'percentage') {
            return max(0, $amount - ($amount * ($dealValue / 100)));
        }

        return $amount;
    }

    public function isDealActive(?Carbon $startAt, ?Carbon $endAt, ?Carbon $at = null): bool
    {
        $at = $at ?? now();

        if ($startAt && $at->lt($startAt)) {
            return false;
        }

        if ($endAt && $at->gt($endAt)) {
            return false;
        }

        return true;
    }
}
