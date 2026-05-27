<?php

namespace App\Http\Controllers;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

/**
 * Product category management. Categories are stored as listing_options rows under the
 * 'product_category' category slug; the storefront filter, the product create/edit form,
 * and the seeder all read from this single source.
 */
class AdminCategoryController extends Controller
{
    public function index(): View
    {
        $categoryId = $this->productCategoryId();

        $rows = $categoryId
            ? ListingOption::query()
                ->where('category_id', $categoryId)
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->orderBy('value')
                ->get(['id', 'value', 'logo_path', 'sort_order', 'is_active'])
            : collect();

        $usage = collect();
        if ($rows->isNotEmpty()) {
            try {
                $usage = Vehicle::query()
                    ->whereIn('product_category_listing_option_id', $rows->pluck('id')->all())
                    ->select('product_category_listing_option_id', DB::raw('count(*) as total'))
                    ->groupBy('product_category_listing_option_id')
                    ->pluck('total', 'product_category_listing_option_id');
            } catch (Throwable $e) {
                Log::error('admin.categories.index usage query failed', ['error' => $e->getMessage()]);
            }
        }

        return view('admin.categories.index', [
            'rows' => $rows,
            'usage' => $usage,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $data = $request->validate([
                'value' => ['required', 'string', 'max:191'],
                'logo_path' => ['nullable', 'string', 'max:512'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $categoryId = $this->productCategoryId(create: true);
            if ($categoryId === 0) {
                return back()
                    ->withInput()
                    ->withErrors(['value' => __('Product category is not configured. Run migrations (php artisan migrate) and try again.')]);
            }

            $value = trim((string) $data['value']);

            if ($this->valueExists($categoryId, $value)) {
                return back()
                    ->withInput()
                    ->withErrors(['value' => __('A category with this name already exists.')]);
            }

            $nextSort = (int) ListingOption::query()
                ->where('category_id', $categoryId)
                ->whereNull('parent_id')
                ->max('sort_order');

            ListingOption::query()->create([
                'category_id' => $categoryId,
                'parent_id' => null,
                'value' => $value,
                'logo_path' => trim((string) ($data['logo_path'] ?? '')) ?: null,
                'sort_order' => $nextSort + 10,
                'is_active' => (bool) ($data['is_active'] ?? true),
            ]);

            return back()->with('status', __('Category added.'));
        } catch (Throwable $e) {
            Log::error('admin.categories.store failed', ['error' => $e->getMessage()]);

            return back()
                ->withInput()
                ->withErrors(['value' => __('Could not save category. Check database migrations and try again.')]);
        }
    }

    public function update(Request $request, ListingOption $category): RedirectResponse
    {
        $this->authorizeProductCategory($category);

        $data = $request->validate([
            'value' => ['required', 'string', 'max:191'],
            'logo_path' => ['nullable', 'string', 'max:512'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $value = trim((string) $data['value']);
        if ($this->valueExists((int) $category->category_id, $value, $category->id)) {
            return back()
                ->withInput()
                ->withErrors(['value' => __('A category with this name already exists.')]);
        }

        $category->update([
            'value' => $value,
            'logo_path' => trim((string) ($data['logo_path'] ?? '')) ?: null,
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', __('Category updated.'));
    }

    public function destroy(ListingOption $category): RedirectResponse
    {
        $this->authorizeProductCategory($category);

        try {
            $usage = Vehicle::query()
                ->where('product_category_listing_option_id', $category->id)
                ->count();
        } catch (Throwable $e) {
            Log::error('admin.categories.destroy usage query failed', ['error' => $e->getMessage()]);

            return back()->withErrors([
                'category' => __('Could not verify category usage. Check database migrations and try again.'),
            ]);
        }

        if ($usage > 0) {
            return back()->withErrors([
                'category' => __('Cannot delete — :count product(s) still use this category.', ['count' => $usage]),
            ]);
        }

        $category->delete();

        return back()->with('status', __('Category deleted.'));
    }

    private function productCategoryId(bool $create = false): int
    {
        $id = (int) ListingOptionCategory::query()->where('slug', 'product_category')->value('id');
        if ($id > 0 || ! $create) {
            return $id;
        }

        $cat = ListingOptionCategory::query()->create([
            'slug' => 'product_category',
            'label' => 'Product category',
            'sort_order' => 5,
        ]);

        return (int) $cat->id;
    }

    private function valueExists(int $categoryId, string $value, ?int $ignoreId = null): bool
    {
        $query = ListingOption::query()
            ->where('category_id', $categoryId)
            ->whereNull('parent_id')
            ->whereRaw('LOWER(value) = ?', [strtolower($value)]);
        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    private function authorizeProductCategory(ListingOption $option): void
    {
        $productCategoryId = $this->productCategoryId();
        abort_if($productCategoryId === 0 || (int) $option->category_id !== $productCategoryId, 404);
    }
}
