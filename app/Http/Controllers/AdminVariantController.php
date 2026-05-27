<?php

namespace App\Http\Controllers;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use App\Models\VehicleVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class AdminVariantController extends Controller
{
    private const ALLOWED_TYPES = ['size', 'color'];

    public function index(Request $request): View
    {
        $type = $this->resolveType($request);
        $categoryId = $this->categoryId($type, create: false);

        $rows = $categoryId
            ? ListingOption::query()
                ->where('category_id', $categoryId)
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->orderBy('value')
                ->get(['id', 'value', 'sort_order', 'is_active'])
            : collect();

        $usage = $rows->isNotEmpty()
            ? $this->usageCounts($type, $rows->pluck('id')->all())
            : collect();

        return view('admin.variants.index', [
            'type' => $type,
            'rows' => $rows,
            'usage' => $usage,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $type = $this->resolveType($request);

        try {
            $data = $request->validate([
                'value' => ['required', 'string', 'max:191'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $categoryId = $this->categoryId($type, create: true);
            if ($categoryId === 0) {
                return back()->withInput()->withErrors(['value' => __('Could not create variant category. Run migrations and try again.')]);
            }

            $value = trim((string) $data['value']);
            if ($this->valueExists($categoryId, $value)) {
                return back()->withInput()->withErrors(['value' => __('This option already exists.')]);
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

            return redirect()
                ->route('admin.variants.index', ['type' => $type])
                ->with('status', __('Variant option added.'));
        } catch (Throwable $e) {
            Log::error('admin.variants.store failed', ['type' => $type, 'error' => $e->getMessage()]);

            return back()
                ->withInput()
                ->withErrors(['value' => __('Could not save variant option. Check migrations and try again.')]);
        }
    }

    public function update(Request $request, ListingOption $variant): RedirectResponse
    {
        $type = $this->resolveType($request);
        $this->authorizeVariant($variant, $type);

        try {
            $data = $request->validate([
                'value' => ['required', 'string', 'max:191'],
                'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $value = trim((string) $data['value']);
            if ($this->valueExists((int) $variant->category_id, $value, $variant->id)) {
                return back()->withInput()->withErrors(['value' => __('This option already exists.')]);
            }

            $variant->update([
                'value' => $value,
                'sort_order' => $data['sort_order'] ?? $variant->sort_order,
                'is_active' => $request->boolean('is_active'),
            ]);

            return redirect()
                ->route('admin.variants.index', ['type' => $type])
                ->with('status', __('Variant option updated.'));
        } catch (Throwable $e) {
            Log::error('admin.variants.update failed', ['id' => $variant->id, 'error' => $e->getMessage()]);

            return back()->withInput()->withErrors(['value' => __('Could not update variant option.')]);
        }
    }

    public function destroy(Request $request, ListingOption $variant): RedirectResponse
    {
        $type = $this->resolveType($request);
        $this->authorizeVariant($variant, $type);

        $usage = (int) ($this->usageCounts($type, [$variant->id])[$variant->id] ?? 0);
        if ($usage > 0) {
            return back()->withErrors([
                'variant' => __('Cannot delete — :count product variant(s) still use this option.', ['count' => $usage]),
            ]);
        }

        $variant->delete();

        return redirect()
            ->route('admin.variants.index', ['type' => $type])
            ->with('status', __('Variant option deleted.'));
    }

    private function resolveType(Request $request): string
    {
        $type = strtolower(trim((string) $request->query('type', $request->input('type', 'size'))));

        return in_array($type, self::ALLOWED_TYPES, true) ? $type : 'size';
    }

    private function categoryId(string $type, bool $create = false): int
    {
        $id = (int) ListingOptionCategory::query()->where('slug', $type)->value('id');
        if ($id > 0 || ! $create) {
            return $id;
        }

        $labels = ['size' => 'Size', 'color' => 'Color'];

        $cat = ListingOptionCategory::query()->create([
            'slug' => $type,
            'label' => $labels[$type] ?? ucfirst($type),
            'sort_order' => $type === 'size' ? 6 : 7,
        ]);

        return (int) $cat->id;
    }

  /**
     * @param  array<int, int>  $optionIds
     * @return \Illuminate\Support\Collection<int, int>
     */
    private function usageCounts(string $type, array $optionIds): \Illuminate\Support\Collection
    {
        if ($optionIds === []) {
            return collect();
        }

        $column = $type === 'size' ? 'size_listing_option_id' : 'color_listing_option_id';

        return VehicleVariant::query()
            ->whereIn($column, $optionIds)
            ->select($column, DB::raw('count(*) as total'))
            ->groupBy($column)
            ->pluck('total', $column);
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

    private function authorizeVariant(ListingOption $option, string $type): void
    {
        $categoryId = $this->categoryId($type);
        abort_if($categoryId === 0 || (int) $option->category_id !== $categoryId, 404);
    }
}
