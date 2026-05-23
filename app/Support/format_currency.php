<?php

declare(strict_types=1);

if (! function_exists('format_currency')) {
    function format_currency($amount): string
    {
        return '₦'.number_format((float) $amount, 2, '.', ',');
    }
}

