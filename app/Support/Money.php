<?php

namespace App\Support;

final class Money
{
    public static function nairaToKobo(int $naira): int
    {
        return max(0, $naira) * 100;
    }

    public static function koboToNaira(int $kobo): float
    {
        return $kobo / 100;
    }

    public static function formatKobo(int $kobo): string
    {
        return format_currency(self::koboToNaira($kobo));
    }
}
