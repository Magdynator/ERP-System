<?php

declare(strict_types=1);

if (! function_exists('erp_money')) {
    function erp_money(float $amount, ?string $currency = null): string
    {
        $currency = $currency ?? config('core.currency', 'USD');

        return number_format($amount, 2) . ' ' . $currency;
    }
}

if (! function_exists('erp_parse_decimal')) {
    function erp_parse_decimal(mixed $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        return (float) str_replace([',', ' '], '', (string) $value);
    }
}
