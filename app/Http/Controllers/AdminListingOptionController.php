<?php

namespace App\Http\Controllers;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use App\Models\Vehicle;
use App\Support\MediaLibraryCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminListingOptionController extends Controller
{
    /** Max rows per page on make, model, and all other catalog option editors. */
    public const OPTIONS_PER_PAGE = 100;

    public function index(): View
    {
        $categories = ListingOptionCategory::query()->orderBy('sort_order')->get();

        $optionCounts = ListingOption::query()
            ->selectRaw('category_id, count(*) as aggregate')
            ->groupBy('category_id')
            ->pluck('aggregate', 'category_id');

        return view('admin.listing-options.index', [
            'categories' => $categories,
            'optionCounts' => $optionCounts,
            'optionsPerPage' => self::OPTIONS_PER_PAGE,
        ]);
    }

    public function show(ListingOptionCategory $category): View
    {
        $options = ListingOption::query()
            ->where('category_id', $category->id)
            ->with('parent')
            ->orderByRaw('parent_id is null desc')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('value')
            ->paginate(self::OPTIONS_PER_PAGE)
            ->withQueryString();

        $makeCategoryId = ListingOptionCategory::query()->where('slug', 'make')->value('id');
        $makeOptions = ($category->slug === 'model' && $makeCategoryId)
            ? ListingOption::query()
                ->where('category_id', (int) $makeCategoryId)
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('value')
                ->get(['id', 'value'])
            : collect();

        return view('admin.listing-options.show', [
            'category' => $category,
            'options' => $options,
            'makeOptions' => $makeOptions,
        ]);
    }

    public function store(Request $request, ListingOptionCategory $category): RedirectResponse
    {
        $isModel = $category->slug === 'model';
        $isMake = $category->slug === 'make';

        $rules = [
            'value' => ['required', 'string', 'max:255'],
            'parent_id' => [$isModel ? 'required' : 'nullable', 'integer', 'exists:listing_options,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
        if ($isMake) {
            $rules['logo_path'] = ['nullable', 'string', 'max:512'];
        }
        $data = $request->validate($rules);

        $parentId = $data['parent_id'] ?? null;
        if ($isModel) {
            $makeCategory = ListingOptionCategory::query()->where('slug', 'make')->firstOrFail();
            $parent = ListingOption::query()->whereKey((int) $parentId)->firstOrFail();
            if ((int) $parent->category_id !== (int) $makeCategory->id || $parent->parent_id !== null) {
                return $this->redirectToCategoryIndex($category)
                    ->withErrors(['parent_id' => __('Choose a valid Make as parent.')])
                    ->withInput();
            }
        } else {
            $parentId = null;
        }

        $maxSort = (int) ListingOption::query()->where('category_id', $category->id)
            ->where('parent_id', $parentId)
            ->max('sort_order');

        $value = ListingOption::normalizedValue($category->slug, $data['value']);
        if ($value === '') {
            return $this->redirectToCategoryIndex($category)
                ->withErrors(['value' => __('Value is required. Please enter a name for this option.')])
                ->withInput();
        }

        if ($duplicateMessage = $this->duplicateOptionMessage($category, $value, $parentId)) {
            return $this->redirectToCategoryIndex($category)
                ->withErrors(['value' => $duplicateMessage])
                ->withInput();
        }

        $option = ListingOption::query()->create([
            'category_id' => $category->id,
            'parent_id' => $parentId,
            'value' => $value,
            'sort_order' => $maxSort + 1,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        if ($isMake && ! empty($data['logo_path'] ?? null)) {
            $path = trim((string) $data['logo_path']);
            if (MediaLibraryCatalog::isPublicMediaPath($path)) {
                $option->update(['logo_path' => $path]);
            }
        }

        return $this->redirectToCategoryIndex($category)->with('status', __('Option added.'));
    }

    public function batchUpdate(Request $request, ListingOptionCategory $category): RedirectResponse
    {
        $allowedIds = ListingOption::query()
            ->where('category_id', $category->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $rules = [
            'options' => ['required', 'array'],
            'options.*.value' => ['required', 'string', 'max:255'],
            'options.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            // Must be validated or Laravel strips unchecked keys from validated() and all rows become inactive.
            'options.*.is_active' => ['sometimes', 'in:1'],
        ];
        if ($category->slug === 'make') {
            $rules['logo_paths'] = ['nullable', 'array'];
            $rules['logo_paths.*'] = ['nullable', 'string', 'max:512'];
        }

        $validated = $request->validate($rules);

        $duplicateErrors = $this->validateBatchNoDuplicates($category, $validated['options'], $allowedIds);
        if ($duplicateErrors !== []) {
            return $this->redirectToCategoryIndex($category)
                ->withErrors($duplicateErrors)
                ->withInput();
        }

        foreach ($validated['options'] as $idStr => $payload) {
            $id = (int) $idStr;
            if (! in_array($id, $allowedIds, true)) {
                continue;
            }
            $option = ListingOption::query()->whereKey($id)->first();
            if (! $option) {
                continue;
            }
            $isActive = array_key_exists('is_active', $payload)
                && ($payload['is_active'] === '1' || $payload['is_active'] === true || $payload['is_active'] === 1);

            $value = ListingOption::normalizedValue($category->slug, (string) ($payload['value'] ?? ''));

            $option->update([
                'value' => $value,
                'sort_order' => isset($payload['sort_order']) ? (int) $payload['sort_order'] : $option->sort_order,
                'is_active' => $isActive,
            ]);
        }

        if ($category->slug === 'make') {
            $logoPaths = $request->input('logo_paths', []);
            if (is_array($logoPaths)) {
                foreach ($logoPaths as $idStr => $path) {
                    $path = trim((string) $path);
                    $id = (int) $idStr;
                    if (! in_array($id, $allowedIds, true)) {
                        continue;
                    }
                    $option = ListingOption::query()->whereKey($id)->first();
                    if (! $option) {
                        continue;
                    }
                    if ($path === '') {
                        if ($option->logo_path && str_starts_with((string) $option->logo_path, 'storage/')) {
                            $rel = substr((string) $option->logo_path, strlen('storage/'));
                            Storage::disk('public')->delete($rel);
                        }
                        $option->update(['logo_path' => null]);

                        continue;
                    }
                    if (! MediaLibraryCatalog::isPublicMediaPath($path)) {
                        continue;
                    }
                    if ($option->logo_path && str_starts_with((string) $option->logo_path, 'storage/')) {
                        $rel = substr((string) $option->logo_path, strlen('storage/'));
                        Storage::disk('public')->delete($rel);
                    }
                    $option->update(['logo_path' => $path]);
                }
            }
        }

        return $this->redirectToCategoryIndex($category)->with('status', __('Changes saved.'));
    }

    public function update(Request $request, ListingOptionCategory $category, ListingOption $option): RedirectResponse
    {
        $this->assertOptionCategory($category, $option);

        $request->validate([
            'value' => ['required', 'string', 'max:255'],
        ]);

        $value = ListingOption::normalizedValue($category->slug, (string) $request->input('value'));
        if ($value === '') {
            return $this->redirectToCategoryIndex($category)
                ->withErrors(['value' => __('Value is required.')]);
        }

        if ($duplicateMessage = $this->duplicateOptionMessage($category, $value, $option->parent_id, $option->id)) {
            return $this->redirectToCategoryIndex($category)
                ->withErrors(['value' => $duplicateMessage]);
        }

        $option->update([
            'value' => $value,
            'is_active' => $request->boolean('is_active'),
        ]);

        return $this->redirectToCategoryIndex($category)->with('status', __('Option updated.'));
    }

    public function destroy(ListingOptionCategory $category, ListingOption $option): RedirectResponse
    {
        $this->assertOptionCategory($category, $option);

        if ($option->children()->exists()) {
            return $this->redirectToCategoryIndex($category)
                ->withErrors(['option' => __('Remove or reassign child model options first.')]);
        }

        $usage = $this->usageCount($category->slug, $option);
        if ($usage > 0) {
            return $this->redirectToCategoryIndex($category)
                ->withErrors(['option' => __('Cannot delete: :count listing(s) still use this value.', ['count' => $usage])]);
        }

        if ($category->slug === 'make' && $option->logo_path && str_starts_with((string) $option->logo_path, 'storage/')) {
            Storage::disk('public')->delete(substr((string) $option->logo_path, strlen('storage/')));
        }

        $option->delete();

        return $this->redirectToCategoryIndex($category)->with('status', __('Option deleted.'));
    }

    public function move(Request $request, ListingOptionCategory $category, ListingOption $option): RedirectResponse
    {
        $this->assertOptionCategory($category, $option);

        $data = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ]);

        $siblingQuery = ListingOption::query()
            ->where('category_id', $category->id)
            ->where('parent_id', $option->parent_id);

        if ($data['direction'] === 'up') {
            $swap = (clone $siblingQuery)->where('sort_order', '<', $option->sort_order)->orderByDesc('sort_order')->first();
        } else {
            $swap = (clone $siblingQuery)->where('sort_order', '>', $option->sort_order)->orderBy('sort_order')->first();
        }

        if ($swap) {
            $a = $option->sort_order;
            $option->update(['sort_order' => $swap->sort_order]);
            $swap->update(['sort_order' => $a]);
        }

        return $this->redirectToCategoryIndex($category);
    }

    /**
     * Redirect to the category editor, keeping ?page= from the previous listing-options URL.
     * (Paginator::withQueryString() does not apply to RedirectResponse.)
     */
    protected function redirectToCategoryIndex(ListingOptionCategory $category): RedirectResponse
    {
        $url = route('admin.listing-options.show', $category);
        $previous = url()->previous();

        if ($previous !== '' && $previous !== $url) {
            $previousPath = parse_url($previous, PHP_URL_PATH) ?? '';
            $targetPath = parse_url($url, PHP_URL_PATH) ?? '';
            if ($previousPath === $targetPath) {
                $query = parse_url($previous, PHP_URL_QUERY);
                if (is_string($query) && $query !== '') {
                    $url .= '?'.$query;
                }
            }
        }

        return redirect()->to($url);
    }

    protected function assertOptionCategory(ListingOptionCategory $category, ListingOption $option): void
    {
        abort_if((int) $option->category_id !== (int) $category->id, 404);
    }

    protected function duplicateOptionMessage(
        ListingOptionCategory $category,
        string $rawValue,
        ?int $parentId,
        ?int $ignoreOptionId = null,
    ): ?string {
        if (! ListingOption::duplicateExists($category->id, $category->slug, $rawValue, $parentId, $ignoreOptionId)) {
            return null;
        }

        return $category->slug === 'model'
            ? __('This model already exists for the selected make.')
            : __('This option already exists.');
    }

    /**
     * @param  array<string, array<string, mixed>>  $optionsPayload
     * @param  list<int>  $allowedIds
     * @return array<string, string>
     */
    protected function validateBatchNoDuplicates(
        ListingOptionCategory $category,
        array $optionsPayload,
        array $allowedIds,
    ): array {
        $errors = [];
        $seenInRequest = [];

        foreach ($optionsPayload as $idStr => $payload) {
            $id = (int) $idStr;
            if (! in_array($id, $allowedIds, true)) {
                continue;
            }

            $option = ListingOption::query()->whereKey($id)->first();
            if (! $option) {
                continue;
            }

            $raw = (string) ($payload['value'] ?? '');
            $comparisonKey = ListingOption::valueComparisonKey($category->slug, $raw);
            if ($comparisonKey === '') {
                continue;
            }

            $parentId = $option->parent_id;
            $groupKey = ($parentId ?? 'root').'|'.$comparisonKey;

            if (isset($seenInRequest[$groupKey])) {
                $errors["options.{$idStr}.value"] = __('Duplicate value in your changes. Each option must have a unique name.');

                continue;
            }
            $seenInRequest[$groupKey] = true;

            if ($duplicateMessage = $this->duplicateOptionMessage($category, $raw, $parentId, $id)) {
                $errors["options.{$idStr}.value"] = $duplicateMessage;
            }
        }

        return $errors;
    }

    /**
     * Count of products that still reference this listing option. After the car
     * column drop only product_category, size, and color remain as live mappings;
     * legacy slugs return 0 so they can be deleted without crashing.
     */
    protected function usageCount(string $categorySlug, ListingOption $option): int
    {
        $column = match ($categorySlug) {
            'product_category' => 'product_category_listing_option_id',
            default => null,
        };
        if ($column === null) {
            return 0;
        }

        return Vehicle::query()->where($column, $option->id)->count();
    }
}
