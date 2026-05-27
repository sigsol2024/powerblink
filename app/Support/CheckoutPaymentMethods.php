<?php

namespace App\Support;

use App\Models\SiteSetting;

final class CheckoutPaymentMethods
{
    /**
     * @return array<int, array{id: string, label: string}>
     */
    public static function enabledForCheckout(): array
    {
        $site = SiteSettingDefaults::mergeWithDatabase(SiteSetting::allKeyed());
        $methods = [];

        if (self::isPaystackEnabled($site)) {
            $methods[] = [
                'id' => 'paystack',
                'label' => __('Paystack'),
            ];
        }

        if (self::isBankTransferEnabled($site)) {
            $methods[] = [
                'id' => 'bank_transfer',
                'label' => __('Bank transfer'),
            ];
        }

        if (self::isPayOnDeliveryEnabled($site)) {
            $methods[] = [
                'id' => 'pay_on_delivery',
                'label' => __('Pay on delivery'),
            ];
        }

        return $methods;
    }

    /**
     * @param  array<string, string>  $site
     */
    public static function isPaystackEnabled(array $site): bool
    {
        if (! self::flagEnabled($site, 'payment_paystack_enabled')) {
            return false;
        }

        $secret = trim((string) config('services.paystack.secret_key', ''));

        return $secret !== '';
    }

    /**
     * @param  array<string, string>  $site
     */
    public static function isBankTransferEnabled(array $site): bool
    {
        return self::flagEnabled($site, 'payment_bank_transfer_enabled');
    }

    /**
     * @param  array<string, string>  $site
     */
    public static function isPayOnDeliveryEnabled(array $site): bool
    {
        return self::flagEnabled($site, 'payment_pay_on_delivery_enabled');
    }

    /**
     * @param  array<string, string>  $site
     */
    public static function bankTransferDetails(array $site): string
    {
        return trim((string) ($site['payment_bank_transfer_details'] ?? ''));
    }

    /**
     * @param  array<string, string>  $site
     */
    public static function payOnDeliveryNote(array $site): string
    {
        return trim((string) ($site['payment_pay_on_delivery_note'] ?? ''));
    }

    /**
     * @param  array<string, string>  $site
     */
    private static function flagEnabled(array $site, string $key): bool
    {
        $value = $site[$key] ?? '0';

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }
}
