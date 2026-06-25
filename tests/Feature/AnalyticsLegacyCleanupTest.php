<?php

namespace Tests\Feature;

use Tests\TestCase;

class AnalyticsLegacyCleanupTest extends TestCase
{
    public function test_admin_analytics_controller_has_no_ecommerce_path_labels(): void
    {
        $source = file_get_contents(app_path('Http/Controllers/AdminAnalyticsController.php'));

        $this->assertIsString($source);
        $this->assertStringNotContainsString("inventory/'", $source);
        $this->assertStringNotContainsString("'compare'", $source);
        $this->assertStringNotContainsString('Listing:', $source);
    }
}
