<?php

namespace App\Support;

class Money
{
    /**
     * Formats an LKR amount stored as whole rupees, e.g. 45000 → "45,000".
     */
    public static function lkr(?int $amount): string
    {
        return $amount === null ? '' : number_format($amount);
    }
}
