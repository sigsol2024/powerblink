<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class PowerblinkSiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site_display_name' => 'Powerblink FC',
            'logo_path' => 'asset/images/powerblink/about-us-powerblink-fc-001.jpg',
            'logo_light_path' => 'asset/images/powerblink/about-us-powerblink-fc-001.jpg',
            'favicon_path' => 'asset/images/powerblink/about-us-powerblink-fc-001.jpg',
            'auth_panel_image_path' => 'asset/images/powerblink/home-powerblink-fc-044.jpg',
            'dealer_phone' => '+234 800 POWERBLINK',
            'dealer_sales_phone' => '+234 800 POWERBLINK',
            'dealer_public_email' => 'info@powerblinkfc.com',
            'contact_notify_email' => 'info@powerblinkfc.com',
            'contact_from_name' => 'Powerblink FC',
            'dealer_address' => 'Plot 42, Powerblink Avenue, Coastal Way, Ibeju Lekki, Lagos, Nigeria',
            'dealer_hours_label' => 'Academy Hours',
            'dealer_sales_hours' => "Monday – Friday: 08:00AM – 06:00PM\nSaturday: 08:00AM – 02:00PM\nSunday: Closed",
            'dealer_service_hours' => "Monday – Friday: 08:00AM – 06:00PM\nSaturday: 08:00AM – 02:00PM\nSunday: Closed",
            'dealer_parts_hours' => "Monday – Friday: 08:00AM – 06:00PM\nSaturday: 08:00AM – 02:00PM\nSunday: Closed",
            'footer_tagline' => 'Elite Excellence in Ibeju Lekki. Shaping the future of football, one star at a time.',
            'footer_about' => 'Powerblink Football Club Limited is a world-class youth academy in Ibeju Lekki, Lagos. We develop disciplined athletes through elite coaching, structured programs, and a safe environment that bridges grassroots talent with professional standards.',
            'copyright_line' => 'Powerblink FC',
            'social_facebook' => 'https://www.facebook.com/powerblinkfc',
            'social_instagram' => 'https://www.instagram.com/powerblinkfc',
            'social_linkedin' => 'https://www.linkedin.com/company/powerblinkfc',
            'social_youtube' => 'https://www.youtube.com/@powerblinkfc',
            'newsletter_enabled' => '1',
            'newsletter_note' => 'Get academy updates, tournament news, and registration openings.',
            'payment_paystack_enabled' => '1',
            'payment_bank_transfer_enabled' => '0',
            'payment_pay_on_delivery_enabled' => '0',
        ];

        foreach ($defaults as $key => $value) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Cache::forget('site_settings_array');
        Cache::store('file')->forget('site_settings_merged_v1');
    }
}
