<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegacySiteChromeRemovedTest extends TestCase
{
    public function test_legacy_header_and_footer_partials_do_not_exist(): void
    {
        $this->assertFileDoesNotExist(resource_path('views/partials/header.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/partials/footer.blade.php'));
        $this->assertFalse(view()->exists('partials.header'));
        $this->assertFalse(view()->exists('partials.footer'));
    }

    public function test_site_layout_does_not_reference_legacy_chrome(): void
    {
        $layout = (string) file_get_contents(resource_path('views/layouts/site.blade.php'));

        $this->assertDoesNotMatchRegularExpression(
            "/@include\s*\(\s*['\"]partials\.header['\"]/",
            $layout
        );
        $this->assertDoesNotMatchRegularExpression(
            "/@include\s*\(\s*['\"]partials\.footer['\"]/",
            $layout
        );
        $this->assertStringContainsString('partials.luxe-store-header', $layout);
    }
}
