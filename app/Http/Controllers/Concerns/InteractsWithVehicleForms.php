<?php

namespace App\Http\Controllers\Concerns;

use App\Models\ListingOption;
use App\Models\ListingOptionCategory;
use App\Models\Vehicle;
use App\Support\VehicleImageUrl;
use App\Support\VehicleListingCatalog;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

trait InteractsWithVehicleForms
{
    private static ?bool $vehiclesHasShowFinancingCalculatorColumn = null;

    /**
     * @return array<string, mixed>
     */
    protected function validateVehicleData(Request $request): array
    {
        $opts = VehicleListingCatalog::filterOptions();
        $makeRows = VehicleListingCatalog::activeMakeSelectRows();
        $matrix = collect($opts['model_matrix'] ?? []);
        $countryRows = VehicleListingCatalog::activeRootOptionRows('country');

        if ($countryRows->isEmpty()) {
            throw ValidationException::withMessages([
                'country_listing_option_id' => __('No countries are configured in Admin → Listing options. Add at least one country before saving a listing.'),
            ]);
        }

        if ($makeRows->isNotEmpty() && $matrix->isEmpty()) {
            throw ValidationException::withMessages([
                'make_listing_option_id' => __('Models are not linked to makes in the catalog yet. Add model rows under each make in Admin → Listing options.'),
            ]);
        }

        $catIds = ListingOptionCategory::query()->pluck('id', 'slug')->all();
        $makeCatId = (int) ($catIds['make'] ?? 0);
        $modelCatId = (int) ($catIds['model'] ?? 0);
        $conditionCatId = (int) ($catIds['condition'] ?? 0);
        $bodyCatId = (int) ($catIds['body_type'] ?? 0);
        $transCatId = (int) ($catIds['transmission'] ?? 0);
        $fuelCatId = (int) ($catIds['fuel_type'] ?? 0);
        $driveCatId = (int) ($catIds['drive'] ?? 0);
        $countryCatId = (int) ($catIds['country'] ?? 0);
        $typeCatId = (int) ($catIds['vehicle_origin_type'] ?? 0);

        $rootRule = function (int $categoryId, bool $required): array {
            if ($categoryId <= 0) {
                return ['nullable', 'integer'];
            }
            $exists = Rule::exists('listing_options', 'id')->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)->whereNull('parent_id')->where('is_active', true);
            });

            return $required ? ['required', 'integer', $exists] : ['nullable', 'integer', $exists];
        };

        $conditionRequired = VehicleListingCatalog::activeRootOptionRows('condition')->isNotEmpty();
        $bodyRequired = VehicleListingCatalog::activeRootOptionRows('body_type')->isNotEmpty();
        $transRequired = VehicleListingCatalog::activeRootOptionRows('transmission')->isNotEmpty();
        $fuelRequired = VehicleListingCatalog::activeRootOptionRows('fuel_type')->isNotEmpty();
        $driveRequired = VehicleListingCatalog::activeRootOptionRows('drive')->isNotEmpty();

        $makeRequired = $makeRows->isNotEmpty() && $matrix->isNotEmpty();
        $modelRequired = $makeRequired;

        $originTypeRows = VehicleListingCatalog::activeRootOptionRows('vehicle_origin_type');
        $typeRequired = $originTypeRows->isNotEmpty() && $typeCatId > 0;

        $exteriorColors = collect($opts['exterior_colors'] ?? []);
        $vehicle = $request->route('vehicle');
        if ($vehicle instanceof Vehicle && trim((string) $vehicle->exterior_color) !== '') {
            if (! $exteriorColors->contains($vehicle->exterior_color)) {
                $exteriorColors->push($vehicle->exterior_color);
            }
        }
        $exteriorColors = $exteriorColors->unique()->values();

        $in = function (?Collection $c): array {
            if ($c === null || ! $c instanceof Collection || $c->isEmpty()) {
                return [];
            }

            return [Rule::in($c->all())];
        };

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:'.(int) date('Y')],
            'make_listing_option_id' => $makeRequired ? $rootRule($makeCatId, true) : ['nullable', 'integer', Rule::exists('listing_options', 'id')->where(function ($q) use ($makeCatId) {
                $q->where('category_id', $makeCatId)->whereNull('parent_id')->where('is_active', true);
            })],
            'model_listing_option_id' => $modelRequired ? ['required', 'integer', Rule::exists('listing_options', 'id')->where(function ($q) use ($modelCatId) {
                $q->where('category_id', $modelCatId)->whereNotNull('parent_id')->where('is_active', true);
            })] : ['nullable', 'integer', Rule::exists('listing_options', 'id')->where(function ($q) use ($modelCatId) {
                $q->where('category_id', $modelCatId)->whereNotNull('parent_id')->where('is_active', true);
            })],
            'price' => ['nullable', 'integer', 'min:0'],
            'mileage' => ['nullable', 'integer', 'min:0'],
            'city_mpg' => ['nullable', 'integer', 'min:0', 'max:200'],
            'hwy_mpg' => ['nullable', 'integer', 'min:0', 'max:200'],
            'condition_listing_option_id' => $rootRule($conditionCatId, $conditionRequired),
            'body_type_listing_option_id' => $rootRule($bodyCatId, $bodyRequired),
            'transmission_listing_option_id' => $rootRule($transCatId, $transRequired),
            'fuel_type_listing_option_id' => $rootRule($fuelCatId, $fuelRequired),
            'drive_listing_option_id' => $rootRule($driveCatId, $driveRequired),
            'street_address' => ['nullable', 'string', 'max:1000'],
            'type_listing_option_id' => $typeRequired ? $rootRule($typeCatId, true) : ['nullable', 'integer'],
            'country_listing_option_id' => $rootRule($countryCatId, true),
            'engine_size' => ['nullable', 'string', 'max:64'],
            'engine_layout' => ['nullable', 'string', 'max:100'],
            'top_track_speed' => ['nullable', 'string', 'max:100'],
            'zero_to_sixty' => ['nullable', 'string', 'max:100'],
            'number_of_gears' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:64'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'map_location' => ['nullable', 'string', 'max:255'],
            'overview' => ['nullable', 'string', 'max:50000'],
            'video_url' => ['nullable', 'url', 'max:2048'],
            'features_text' => ['nullable', 'string', 'max:10000'],
            'exterior_color' => array_merge(['nullable', 'string', 'max:255'], $in($exteriorColors)),
            'interior_color' => ['nullable', 'string', 'max:255'],
            'vin' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:50000'],
            'tech_specs' => ['nullable', 'array'],
            'tech_specs.engine_layout' => ['nullable', 'string', 'max:100'],
            'tech_specs.engine_volume' => ['nullable', 'string', 'max:100'],
            'tech_specs.drive_type' => ['nullable', 'string', 'max:100'],
            'tech_specs.top_speed' => ['nullable', 'string', 'max:100'],
            'tech_specs.zero_to_70' => ['nullable', 'string', 'max:100'],
            'tech_specs.transmission_gears' => ['nullable', 'string', 'max:100'],
            'main_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'main_image_path' => ['nullable', 'string', 'max:2048', 'regex:/^(https?:\/\/|\/?(asset|storage)\/).+/i'],
            'images' => ['sometimes', 'array', 'max:50'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'gallery_image_paths' => ['sometimes', 'array', 'max:50'],
            'gallery_image_paths.*' => ['string', 'max:2048', 'regex:/^(https?:\/\/|\/?(asset|storage)\/).+/i'],
            'remove_image_ids' => ['sometimes', 'array', 'max:100'],
            'remove_image_ids.*' => ['integer', 'min:1'],
        ]);

        $makeId = (int) ($data['make_listing_option_id'] ?? 0);
        $modelId = (int) ($data['model_listing_option_id'] ?? 0);
        if ($makeId > 0 && $modelId > 0) {
            $parentId = (int) (ListingOption::query()->whereKey($modelId)->value('parent_id') ?? 0);
            if ($parentId !== $makeId) {
                throw ValidationException::withMessages([
                    'model_listing_option_id' => __('Model does not match the selected make.'),
                ]);
            }
            if ($matrix instanceof Collection && $matrix->isNotEmpty()) {
                VehicleListingCatalog::assertMakeModelPairById($matrix, $makeId, $modelId);
            }
        }

        $nigerianTypeId = VehicleListingCatalog::vehicleOriginTypeIdByLabel('Nigerian');
        $nigeriaCountryId = VehicleListingCatalog::nigeriaCountryListingOptionId();
        if ($typeRequired && $nigerianTypeId) {
            $typeId = (int) ($data['type_listing_option_id'] ?? 0);
            if ($typeId === $nigerianTypeId) {
                if (! $nigeriaCountryId) {
                    throw ValidationException::withMessages([
                        'type_listing_option_id' => __('A “Nigeria” country option must exist in Admin → Listing options to use the Nigerian type.'),
                    ]);
                }
                $data['country_listing_option_id'] = $nigeriaCountryId;
            }
        }

        if (! $typeRequired) {
            unset($data['type_listing_option_id']);
        }

        $data['is_special'] = $request->boolean('is_special');

        $data['features'] = $this->parseFeatures($data['features_text'] ?? null);
        unset($data['features_text']);

        $driveId = (int) ($data['drive_listing_option_id'] ?? 0);
        $driveLabel = $driveId > 0
            ? (string) (ListingOption::query()->whereKey($driveId)->value('value') ?? '')
            : '';

        $rawTechSpecs = [
            'engine_layout' => (string) ($data['tech_specs']['engine_layout'] ?? $data['engine_layout'] ?? ''),
            'engine_volume' => (string) ($data['tech_specs']['engine_volume'] ?? $data['engine_size'] ?? ''),
            'drive_type' => (string) ($data['tech_specs']['drive_type'] ?? $driveLabel),
            'top_speed' => (string) ($data['tech_specs']['top_speed'] ?? $data['top_track_speed'] ?? ''),
            'zero_to_70' => (string) ($data['tech_specs']['zero_to_70'] ?? $data['zero_to_sixty'] ?? ''),
            'transmission_gears' => (string) ($data['tech_specs']['transmission_gears'] ?? $data['number_of_gears'] ?? ''),
        ];

        if ($vehicle instanceof Vehicle) {
            $existing = is_array($vehicle->tech_specs) ? $vehicle->tech_specs : [];
            $preserveFromStored = [
                'engine_layout' => 'engine_layout',
                'top_speed' => 'top_track_speed',
                'zero_to_70' => 'zero_to_sixty',
                'transmission_gears' => 'number_of_gears',
            ];
            foreach ($preserveFromStored as $specKey => $column) {
                if (trim((string) ($rawTechSpecs[$specKey] ?? '')) !== '') {
                    continue;
                }
                $fromJson = trim((string) ($existing[$specKey] ?? ''));
                if ($fromJson !== '') {
                    $rawTechSpecs[$specKey] = $fromJson;

                    continue;
                }
                $fromCol = trim((string) ($vehicle->getAttribute($column) ?? ''));
                if ($fromCol !== '') {
                    $rawTechSpecs[$specKey] = $fromCol;
                }
            }
        }

        $data['tech_specs'] = collect($rawTechSpecs)
            ->map(fn ($value) => trim($value))
            ->filter(fn ($value) => $value !== '')
            ->all();
        if ($data['tech_specs'] === []) {
            $data['tech_specs'] = null;
        }

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

    private function vehiclesHasShowFinancingCalculatorColumn(): bool
    {
        if (self::$vehiclesHasShowFinancingCalculatorColumn !== null) {
            return self::$vehiclesHasShowFinancingCalculatorColumn;
        }

        try {
            self::$vehiclesHasShowFinancingCalculatorColumn = Schema::hasColumn('vehicles', 'show_financing_calculator');
        } catch (\Throwable) {
            self::$vehiclesHasShowFinancingCalculatorColumn = false;
        }

        return self::$vehiclesHasShowFinancingCalculatorColumn;
    }
}
