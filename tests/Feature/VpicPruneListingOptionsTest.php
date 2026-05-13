<?php

namespace Tests\Feature;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class VpicPruneListingOptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_prune_dry_run_counts_unused_non_allowlisted_vpic_rows(): void
    {
        Config::set('vpic.allowed_make_ids', [448]);

        $makeCatId = (int) ListingOptionCategory::query()->where('slug', 'make')->value('id');
        $modelCatId = (int) ListingOptionCategory::query()->where('slug', 'model')->value('id');

        $junkMake = ListingOption::query()->create([
            'category_id' => $makeCatId,
            'parent_id' => null,
            'value' => 'JunkCo',
            'sort_order' => 1,
            'is_active' => true,
            'external_source' => ListingOption::EXTERNAL_SOURCE_VPIC,
            'external_id' => '777777',
            'last_synced_at' => now(),
            'source_payload' => [],
        ]);

        ListingOption::query()->create([
            'category_id' => $modelCatId,
            'parent_id' => $junkMake->id,
            'value' => 'JunkModel',
            'sort_order' => 1,
            'is_active' => true,
            'external_source' => ListingOption::EXTERNAL_SOURCE_VPIC,
            'external_id' => '1',
            'last_synced_at' => now(),
            'source_payload' => [],
        ]);

        $this->artisan('listing-options:prune-vpic', ['--dry-run' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('models deleted: 1')
            ->expectsOutputToContain('makes deleted: 1');

        $this->assertDatabaseHas('listing_options', ['id' => $junkMake->id]);
    }

    public function test_prune_deletes_unused_non_allowlisted_vpic_rows(): void
    {
        Config::set('vpic.allowed_make_ids', [448]);

        $makeCatId = (int) ListingOptionCategory::query()->where('slug', 'make')->value('id');
        $modelCatId = (int) ListingOptionCategory::query()->where('slug', 'model')->value('id');

        $junkMake = ListingOption::query()->create([
            'category_id' => $makeCatId,
            'parent_id' => null,
            'value' => 'JunkCo',
            'sort_order' => 1,
            'is_active' => true,
            'external_source' => ListingOption::EXTERNAL_SOURCE_VPIC,
            'external_id' => '777778',
            'last_synced_at' => now(),
            'source_payload' => [],
        ]);

        $junkModel = ListingOption::query()->create([
            'category_id' => $modelCatId,
            'parent_id' => $junkMake->id,
            'value' => 'JunkModel',
            'sort_order' => 1,
            'is_active' => true,
            'external_source' => ListingOption::EXTERNAL_SOURCE_VPIC,
            'external_id' => '2',
            'last_synced_at' => now(),
            'source_payload' => [],
        ]);

        $this->artisan('listing-options:prune-vpic')
            ->assertSuccessful();

        $this->assertDatabaseMissing('listing_options', ['id' => $junkMake->id]);
        $this->assertDatabaseMissing('listing_options', ['id' => $junkModel->id]);
    }

    public function test_prune_does_not_touch_make_referenced_by_vehicle(): void
    {
        Config::set('vpic.allowed_make_ids', [448]);

        $makeCatId = (int) ListingOptionCategory::query()->where('slug', 'make')->value('id');

        $user = User::factory()->create();

        $usedMake = ListingOption::query()->create([
            'category_id' => $makeCatId,
            'parent_id' => null,
            'value' => 'UsedJunk',
            'sort_order' => 1,
            'is_active' => true,
            'external_source' => ListingOption::EXTERNAL_SOURCE_VPIC,
            'external_id' => '777779',
            'last_synced_at' => now(),
            'source_payload' => [],
        ]);

        Vehicle::factory()->create([
            'user_id' => $user->id,
            'make_listing_option_id' => $usedMake->id,
        ]);

        $this->artisan('listing-options:prune-vpic')
            ->assertSuccessful();

        $this->assertDatabaseHas('listing_options', ['id' => $usedMake->id]);
    }

    public function test_prune_does_not_delete_make_with_manual_model_child(): void
    {
        Config::set('vpic.allowed_make_ids', [448]);

        $makeCatId = (int) ListingOptionCategory::query()->where('slug', 'make')->value('id');
        $modelCatId = (int) ListingOptionCategory::query()->where('slug', 'model')->value('id');

        $vpicMake = ListingOption::query()->create([
            'category_id' => $makeCatId,
            'parent_id' => null,
            'value' => 'VpicParent',
            'sort_order' => 1,
            'is_active' => true,
            'external_source' => ListingOption::EXTERNAL_SOURCE_VPIC,
            'external_id' => '888001',
            'last_synced_at' => now(),
            'source_payload' => [],
        ]);

        ListingOption::query()->create([
            'category_id' => $modelCatId,
            'parent_id' => $vpicMake->id,
            'value' => 'Manual Model',
            'sort_order' => 1,
            'is_active' => true,
            'external_source' => null,
            'external_id' => null,
        ]);

        $this->artisan('listing-options:prune-vpic')
            ->assertSuccessful();

        $this->assertDatabaseHas('listing_options', ['id' => $vpicMake->id]);
    }

    public function test_prune_fails_when_allowlist_empty(): void
    {
        Config::set('vpic.allowed_make_ids', []);

        $this->artisan('listing-options:prune-vpic')
            ->assertFailed();
    }
}
