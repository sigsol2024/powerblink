<?php

namespace App\Http\Controllers;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

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
                ->get(['id', 'value', 'sort_order', 'is_active'])
            : collect();

        $usage = $rows->isNotEmpty()
            ? Vehicle::query()
                ->whereIn('product_category_listing_option_id', $rows->pluck('id')->all())
                ->select('product_category_listing_option_id', DB::raw('count(*) as total'))
                ->groupBy('product_category_listing_option_id')
                ->pluck('total', 'product_category_listing_option_id')
            : collect();

        return view('admin.categories.index', [
            'rows' => $rows,
            'usage' => $usage,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'value' => ['required', 'string', 'max:191'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $categoryId = $this->productCategoryId(create: true);
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
            'sort_order' => $nextSort + 10,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return back()->with('status', __('Category added.'));
    }

    public function update(Request $request, ListingOption $category): RedirectResponse
    {
        $this->authorizeProductCategory($category);

        $data = $request->validate([
            'value' => ['required', 'string', 'max:191'],
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
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', __('Category updated.'));
    }

    public function destroy(ListingOption $category): RedirectResponse
    {
        $this->authorizeProductCategory($category);

        $usage = Vehicle::query()
            ->where('product_category_listing_option_id', $category->id)
            ->count();

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
