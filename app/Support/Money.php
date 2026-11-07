<?php

namespace App\Support;

final class Money
{
    public static function idr(float|int|string|null $value): string
    {
        return 'Rp '.number_format((float) $value, 0, ',', '.');
    }

    public static function numeric(float|int|string|null $value): string
    {
        $number = (float) $value;
        $decimals = fmod($number, 1.0) === 0.0 ? 0 : 2;

        return number_format($number, $decimals, ',', '.');
    }
}
