<?php

namespace Tests\Feature;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VpicListingOptionsSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    private function makeCategoryId(): int
    {
        return (int) ListingOptionCategory::query()->where('slug', 'make')->value('id');
    }

    private function modelCategoryId(): int
    {
        return (int) ListingOptionCategory::query()->where('slug', 'model')->value('id');
    }

    public function test_sync_creates_vpic_make_with_source_payload(): void
    {
        Config::set('vpic.allowed_make_ids', [999991]);

        Http::fake([
            '*vpic.nhtsa.dot.gov*GetAllMakes*' => Http::response([
                'Count' => 1,
                'Message' => 'OK',
                'Results' => [
                    ['Make_ID' => 999991, 'Make_Name' => 'TESTMAKEBRAND'],
                ],
            ], 200),
        ]);

        $this->artisan('listing-options:sync-vpic', ['--makes-only' => true])
            ->assertSuccessful();

        $row = ListingOption::query()
            ->where('category_id', $this->makeCategoryId())
            ->whereNull('parent_id')
            ->where('external_source', ListingOption::EXTERNAL_SOURCE_VPIC)
            ->where('external_id', '999991')
            ->first();

        $this->assertNotNull($row);
        $this->assertSame('TESTMAKEBRAND', $row->value);
        $this->assertIsArray($row->source_payload);
        $this->assertSame(999991, $row->source_payload['Make_ID'] ?? null);
        $this->assertSame('TESTMAKEBRAND', $row->source_payload['Make_Name'] ?? null);
        $this->assertNotNull($row->last_synced_at);
    }

    public function test_sync_skips_insert_when_manual_make_name_collides(): void
    {
        Config::set('vpic.allowed_make_ids', [474]);

        $makeCatId = $this->makeCategoryId();

        ListingOption::query()->create([
            'category_id' => $makeCatId,
            'parent_id' => null,
            'value' => 'Honda',
            'sort_order' => 1,
            'is_active' => true,
            'external_source' => null,
            'external_id' => null,
        ]);

        Http::fake([
            '*vpic.nhtsa.dot.gov*GetAllMakes*' => Http::response([
                'Count' => 1,
                'Results' => [
                    ['Make_ID' => 474, 'Make_Name' => 'HONDA'],
                ],
            ], 200),
        ]);

        $this->artisan('listing-options:sync-vpic', ['--makes-only' => true])
            ->assertSuccessful();

        $this->assertSame(
            1,
            ListingOption::query()->where('category_id', $makeCatId)->whereNull('parent_id')->count()
        );
        $this->assertNull(
            ListingOption::query()
                ->where('category_id', $makeCatId)
                ->where('external_id', '474')
                ->value('id')
        );
    }

    public function test_sync_models_for_vpic_parent_make(): void
    {
        Config::set('vpic.allowed_make_ids', [474]);

        $makeCatId = $this->makeCategoryId();
        $modelCatId = $this->modelCategoryId();

        $parent = ListingOption::query()->create([
            'category_id' => $makeCatId,
            'parent_id' => null,
            'value' => 'Honda',
            'sort_order' => 1,
            'is_active' => true,
            'external_source' => ListingOption::EXTERNAL_SOURCE_VPIC,
            'external_id' => '474',
            'last_synced_at' => now(),
            'source_payload' => ['Make_ID' => 474, 'Make_Name' => 'HONDA'],
        ]);

        Http::fake([
            '*vpic.nhtsa.dot.gov*GetModelsForMakeId*474*' => Http::response([
                'Count' => 1,
                'Results' => [
                    ['Make_ID' => 474, 'Make_Name' => 'Honda', 'Model_ID' => 18631, 'Model_Name' => 'Accord'],
                ],
            ], 200),
        ]);

        $this->artisan('listing-options:sync-vpic', ['--models-only' => true])
            ->assertSuccessful();

        $model = ListingOption::query()
            ->where('category_id', $modelCatId)
            ->where('parent_id', $parent->id)
            ->where('external_source', ListingOption::EXTERNAL_SOURCE_VPIC)
            ->where('external_id', '18631')
            ->first();

        $this->assertNotNull($model);
        $this->assertSame('Accord', $model->value);
        $this->assertIsArray($model->source_payload);
        $this->assertSame(18631, $model->source_payload['Model_ID'] ?? null);
    }

    public function test_dry_run_does_not_persist_makes(): void
    {
        Config::set('vpic.allowed_make_ids', [888881]);

        Http::fake([
            '*vpic.nhtsa.dot.gov*GetAllMakes*' => Http::response([
                'Results' => [
                    ['Make_ID' => 888881, 'Make_Name' => 'DRYRUNMAKE'],
                ],
            ], 200),
        ]);

        $this->artisan('listing-options:sync-vpic', ['--makes-only' => true, '--dry-run' => true])
            ->assertSuccessful();

        $this->assertNull(
            ListingOption::query()
                ->where('external_id', '888881')
                ->value('id')
        );
    }

    public function test_sync_skips_makes_not_on_allowlist(): void
    {
        Config::set('vpic.allowed_make_ids', [111]);

        Http::fake([
            '*vpic.nhtsa.dot.gov*GetAllMakes*' => Http::response([
                'Results' => [
                    ['Make_ID' => 111, 'Make_Name' => 'ALLOWEDMAKE'],
                    ['Make_ID' => 222, 'Make_Name' => 'NOTALLOWED'],
                ],
            ], 200),
        ]);

        $this->artisan('listing-options:sync-vpic', ['--makes-only' => true])
            ->expectsOutputToContain('skipped not allowlisted');

        $this->assertNotNull(ListingOption::query()->where('external_id', '111')->first());
        $this->assertNull(ListingOption::query()->where('external_id', '222')->first());
    }

    public function test_models_sync_only_queries_allowlisted_parents(): void
    {
        Config::set('vpic.allowed_make_ids', [474]);

        $makeCatId = $this->makeCategoryId();
        $modelCatId = $this->modelCategoryId();

        ListingOption::query()->create([
            'category_id' => $makeCatId,
            'parent_id' => null,
            'value' => 'Disallowed',
            'sort_order' => 1,
            'is_active' => true,
            'external_source' => ListingOption::EXTERNAL_SOURCE_VPIC,
            'external_id' => '999888',
            'last_synced_at' => now(),
            'source_payload' => [],
        ]);

        ListingOption::query()->create([
            'category_id' => $makeCatId,
            'parent_id' => null,
            'value' => 'Honda',
            'sort_order' => 2,
            'is_active' => true,
            'external_source' => ListingOption::EXTERNAL_SOURCE_VPIC,
            'external_id' => '474',
            'last_synced_at' => now(),
            'source_payload' => [],
        ]);

        Http::fake([
            '*vpic.nhtsa.dot.gov*GetModelsForMakeId*474*' => Http::response([
                'Results' => [
                    ['Model_ID' => 1, 'Model_Name' => 'Civic'],
                ],
            ], 200),
        ]);

        $this->artisan('listing-options:sync-vpic', ['--models-only' => true])
            ->assertSuccessful();
    }

    public function test_sync_fails_when_allowlist_empty(): void
    {
        Config::set('vpic.allowed_make_ids', []);

        Http::fake([
            '*vpic.nhtsa.dot.gov*GetAllMakes*' => Http::response(['Results' => []], 200),
        ]);

        $this->artisan('listing-options:sync-vpic', ['--makes-only' => true])
            ->assertFailed();
    }
}
