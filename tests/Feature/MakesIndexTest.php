<?php

namespace Tests\Feature;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MakesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_makes_page_renders_when_makes_exist(): void
    {
        $makeCatId = (int) ListingOptionCategory::query()->where('slug', 'make')->value('id');
        ListingOption::query()->create([
            'category_id' => $makeCatId,
            'parent_id' => null,
            'value' => 'TestMake',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get('/makes');
        $response->assertOk();
        $response->assertSee('Search by make', false);
        $response->assertSee('TestMake', false);
    }
}
