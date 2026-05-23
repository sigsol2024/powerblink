<?php

namespace Tests\Feature;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminListingOptionDuplicateTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $this->seed(RolesSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    public function test_admin_cannot_add_duplicate_make(): void
    {
        $admin = $this->makeAdmin();
        $makeCategory = ListingOptionCategory::query()->where('slug', 'make')->firstOrFail();

        ListingOption::query()->create([
            'category_id' => $makeCategory->id,
            'parent_id' => null,
            'value' => 'TOYOTA',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.listing-options.show', $makeCategory))
            ->post(route('admin.listing-options.store', $makeCategory), [
                'value' => 'toyota',
            ])
            ->assertSessionHasErrors('value')
            ->assertRedirect(route('admin.listing-options.show', $makeCategory));

        $this->assertSame(1, ListingOption::query()->where('category_id', $makeCategory->id)->count());
    }

    public function test_admin_cannot_add_duplicate_model_for_same_make(): void
    {
        $admin = $this->makeAdmin();
        $makeCategory = ListingOptionCategory::query()->where('slug', 'make')->firstOrFail();
        $modelCategory = ListingOptionCategory::query()->where('slug', 'model')->firstOrFail();

        $make = ListingOption::query()->create([
            'category_id' => $makeCategory->id,
            'parent_id' => null,
            'value' => 'HONDA',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        ListingOption::query()->create([
            'category_id' => $modelCategory->id,
            'parent_id' => $make->id,
            'value' => 'Civic',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.listing-options.show', $modelCategory))
            ->post(route('admin.listing-options.store', $modelCategory), [
                'value' => 'civic',
                'parent_id' => $make->id,
            ])
            ->assertSessionHasErrors('value')
            ->assertRedirect(route('admin.listing-options.show', $modelCategory));

        $this->assertSame(1, ListingOption::query()->where('category_id', $modelCategory->id)->count());
    }

    public function test_admin_batch_update_rejects_renaming_to_existing_value(): void
    {
        $admin = $this->makeAdmin();
        $conditionCategory = ListingOptionCategory::query()->where('slug', 'condition')->firstOrFail();

        $used = ListingOption::query()->create([
            'category_id' => $conditionCategory->id,
            'parent_id' => null,
            'value' => 'Used',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $new = ListingOption::query()->create([
            'category_id' => $conditionCategory->id,
            'parent_id' => null,
            'value' => 'Brand new',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.listing-options.show', $conditionCategory))
            ->put(route('admin.listing-options.batch-update', $conditionCategory), [
                'options' => [
                    (string) $used->id => ['value' => 'Used', 'sort_order' => 1, 'is_active' => '1'],
                    (string) $new->id => ['value' => 'used', 'sort_order' => 2, 'is_active' => '1'],
                ],
            ])
            ->assertSessionHasErrors('options.'.$new->id.'.value')
            ->assertRedirect(route('admin.listing-options.show', $conditionCategory));

        $this->assertSame('Brand new', $new->fresh()->value);
    }
}
