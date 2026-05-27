<?php

namespace App\Http\Controllers\Concerns;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use App\Models\Vehicle;
use App\Services\ProductVariantSyncService;
use App\Support\VehicleImageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

trait InteractsWithVehicleForms
{
    /**
     * Lean apparel-store product validation. Title, category, price, stock, copy,
     * SKU, images, visibility. The legacy car-shape (make/model/fuel/transmission/etc.)
     * is gone; columns will be dropped once category code is wired up.
     *
     * @return array<string, mixed>
     */
    protected function validateVehicleData(Request $request): array
    {
        $productCategoryCatId = (int) (ListingOptionCategory::query()
            ->where('slug', 'product_category')
            ->value('id') ?? 0);

        $categoryRule = ['nullable', 'integer'];
        if ($productCategoryCatId > 0) {
            $categoryRule[] = Rule::exists('listing_options', 'id')->where(function ($q) use ($productCategoryCatId) {
                $q->where('category_id', $productCategoryCatId)->where('is_active', true);
            });
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'product_category_listing_option_id' => $categoryRule,
            'price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'overview' => ['nullable', 'string', 'max:50000'],
            'composition_care' => ['nullable', 'string', 'max:50000'],
            'shipping_returns' => ['nullable', 'string', 'max:50000'],
            'description' => ['nullable', 'string', 'max:50000'],
            'features_text' => ['nullable', 'string', 'max:10000'],
            'vin' => ['nullable', 'string', 'max:255'],
            'main_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'main_image_path' => ['nullable', 'string', 'max:2048', 'regex:/^(https?:\/\/|\/?(asset|storage)\/).+/i'],
            'images' => ['sometimes', 'array', 'max:50'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'gallery_image_paths' => ['sometimes', 'array', 'max:50'],
            'gallery_image_paths.*' => ['string', 'max:2048', 'regex:/^(https?:\/\/|\/?(asset|storage)\/).+/i'],
            'remove_image_ids' => ['sometimes', 'array', 'max:100'],
            'remove_image_ids.*' => ['integer', 'min:1'],
        ]);

        $data['is_special'] = $request->boolean('is_special');

        $data['features'] = $this->parseFeatures($data['features_text'] ?? null);
        unset($data['features_text']);

        return $data;
    }

    /**
     * @return array<int, string>|null
     */
    protected function parseFeatures(?string $text): ?array
    {
        if ($text === null || trim($text) === '') {
            return null;
        }

        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
        $out = [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line !== '') {
                $out[] = $line;
            }
        }

        if ($out === []) {
            return null;
        }

        return array_values(array_unique($out));
    }

    protected function uniqueSlug(string $title, ?int $ignoreVehicleId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;
        while (Vehicle::query()
            ->where('slug', $slug)
            ->when($ignoreVehicleId, fn ($query) => $query->where('id', '!=', $ignoreVehicleId))
            ->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    protected function storeUploadedImages(Request $request, Vehicle $vehicle): void
    {
        $nextSortOrder = (int) $vehicle->images()->max('sort_order');

        $mainImagePath = $this->normalizeSelectedImagePath((string) $request->input('main_image_path', ''));
        if ($request->hasFile('main_image') || $mainImagePath !== '') {
            $vehicle->images()->increment('sort_order');
            $vehicle->images()->create([
                'path' => $request->hasFile('main_image')
                    ? 'storage/'.$this->storeImageOnPublicDisk($request->file('main_image'), $vehicle)
                    : $mainImagePath,
                'sort_order' => 1,
            ]);
            $nextSortOrder = (int) $vehicle->images()->max('sort_order');
        }

        $galleryPaths = collect($request->input('gallery_image_paths', []))
            ->map(fn ($path) => $this->normalizeSelectedImagePath((string) $path))
            ->filter(fn ($path) => $path !== '')
            ->values()
            ->all();

        if (! $request->hasFile('images') && $galleryPaths === []) {
            return;
        }

        foreach ($galleryPaths as $galleryPath) {
            $nextSortOrder++;
            $vehicle->images()->create([
                'path' => $galleryPath,
                'sort_order' => $nextSortOrder,
            ]);
        }

        foreach ($request->file('images', []) as $uploadedImage) {
            $nextSortOrder++;
            $stored = $this->storeImageOnPublicDisk($uploadedImage, $vehicle);
            $vehicle->images()->create([
                'path' => 'storage/'.$stored,
                'sort_order' => $nextSortOrder,
            ]);
        }
    }

    protected function normalizeSelectedImagePath(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $path) === 1) {
            return $path;
        }

        return ltrim($path, '/');
    }

    protected function storeImageOnPublicDisk(UploadedFile $uploadedImage, Vehicle $vehicle): string
    {
        $extension = match ($uploadedImage->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
        $filename = (string) Str::uuid().'.'.$extension;
        $dir = config('media.listing_photos_directory', 'listings/vehicles').'/'.$vehicle->id;
        $stored = $uploadedImage->storePubliclyAs($dir, $filename, 'public');
        if (! is_string($stored) || $stored === '') {
            throw ValidationException::withMessages([
                'images' => __('An image could not be uploaded. Please try again.'),
            ]);
        }

        return $stored;
    }

    protected function resequenceImages(Vehicle $vehicle): void
    {
        foreach ($vehicle->images()->orderBy('sort_order')->get()->values() as $index => $image) {
            $image->update(['sort_order' => $index + 1]);
        }
    }

    /**
     * Relative path under the public disk, or null when the row stores a remote URL (no file to delete).
     */
    protected function relativeStoragePathForDelete(string $publicPath): ?string
    {
        if (VehicleImageUrl::isRemote($publicPath)) {
            return null;
        }

        return ltrim(Str::after($publicPath, 'storage/'), '/');
    }

    protected function deleteLocalVehicleImageFiles(Vehicle $vehicle): void
    {
        foreach ($vehicle->images as $image) {
            $rel = $this->relativeStoragePathForDelete($image->path);
            if ($rel !== null) {
                Storage::disk('public')->delete($rel);
            }
        }
    }

    protected function syncProductVariants(Request $request, Vehicle $vehicle): void
    {
        $dimensions = array_values(array_intersect(
            ['size', 'color'],
            array_map('strval', (array) $request->input('variant_dimensions', []))
        ));

        $options = [
            'size' => array_map('intval', (array) $request->input('dimension_options.size', [])),
            'color' => array_map('intval', (array) $request->input('dimension_options.color', [])),
        ];

        $matrix = (array) $request->input('variant_matrix', []);

        app(ProductVariantSyncService::class)->sync($vehicle, $dimensions, $options, $matrix);
    }

    /**
     * @return array{
     *   sizeOptions: \Illuminate\Support\Collection,
     *   colorOptions: \Illuminate\Support\Collection,
     *   selectedDimensions: array<int, string>,
     *   selectedSizeIds: array<int, int>,
     *   selectedColorIds: array<int, int>,
     *   variantMatrix: array<string, array<string, mixed>>
     * }
     */
    protected function variantFormContext(?Vehicle $vehicle = null): array
    {
        $service = app(ProductVariantSyncService::class);
        $sizeOptions = $this->listingOptionsForSlug('size');
        $colorOptions = $this->listingOptionsForSlug('color');
        $selectedDimensions = [];
        $selectedSizeIds = [];
        $selectedColorIds = [];
        $variantMatrix = [];

        if ($vehicle !== null) {
            $variants = $vehicle->allVariants()->get();
            if ($variants->isNotEmpty()) {
                if ($variants->contains(fn ($v) => $v->size_listing_option_id)) {
                    $selectedDimensions[] = 'size';
                }
                if ($variants->contains(fn ($v) => $v->color_listing_option_id)) {
                    $selectedDimensions[] = 'color';
                }
                $selectedSizeIds = $variants->pluck('size_listing_option_id')->filter()->unique()->values()->all();
                $selectedColorIds = $variants->pluck('color_listing_option_id')->filter()->unique()->values()->all();
                foreach ($variants as $variant) {
                    $key = $service->matrixKey($variant->size_listing_option_id, $variant->color_listing_option_id);
                    $variantMatrix[$key] = [
                        'stock' => $variant->stock,
                        'sku' => $variant->sku,
                        'price' => $variant->price,
                    ];
                }
            }
        }

        return compact('sizeOptions', 'colorOptions', 'selectedDimensions', 'selectedSizeIds', 'selectedColorIds', 'variantMatrix');
    }

    protected function listingOptionsForSlug(string $slug): \Illuminate\Support\Collection
    {
        return ListingOption::query()
            ->whereHas('category', fn ($q) => $q->where('slug', $slug))
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('value')
            ->get(['id', 'value']);
    }
}
