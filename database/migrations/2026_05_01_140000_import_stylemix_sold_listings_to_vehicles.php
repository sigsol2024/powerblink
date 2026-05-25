<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Imports sold demo listings from Stylemix Motors (Elementor Dealership Two) into `vehicles` + `vehicle_images`.
 *
 * - Slugs are prefixed with `stylemix-sold-import-` + source path slug (idempotent + rollback-safe).
 * - Set IMPORT_STYLEMIX_SOLD_LISTINGS=false in .env to skip; in testing it defaults off unless explicitly true.
 *
 * @see https://motors.stylemixthemes.com/elementor-dealer-two/inventory/?listing_status=sold
 */
return new class extends Migration
{
    private const BASE = 'https://motors.stylemixthemes.com';

    private const INVENTORY_PATH = '/elementor-dealer-two/inventory/';

    private const LISTINGS_PREFIX = '/elementor-dealer-two/listings/';

    private const SLUG_PREFIX = 'stylemix-sold-import-';

    private const MAX_INVENTORY_PAGES = 30;

    private const HTTP_TIMEOUT_SEC = 45;

    public function up(): void
    {
        if (! $this->shouldRunImport()) {
            return;
        }

        try {
            $admin = User::query()->role('admin')->orderBy('id')->first()
                ?: User::query()->orderBy('id')->first();
        } catch (\Throwable $e) {
            Log::warning('Stylemix sold import skipped: roles/users not seeded yet ('.$e->getMessage().').');

            return;
        }

        if ($admin === null) {
            Log::warning('Stylemix sold import skipped: no users exist yet. Run migrations after seeding/bootstrapping a user or run `php artisan db:seed` then re-invoke imports manually.');

            return;
        }

        if (! $admin->hasRole('admin')) {
            Log::warning('Stylemix sold import: assigning listings to fallback user '.$admin->email.' (no admin user found yet — adjust ownership in dashboard later).');
        }

        $pathSlugs = $this->discoverSoldListingPathSlugs();
        if ($pathSlugs === []) {
            Log::warning('Stylemix sold import: no listing URLs discovered (remote site may be unreachable).');

            return;
        }

        $hasShowFinancing = Schema::hasColumn('vehicles', 'show_financing_calculator');

        foreach ($pathSlugs as $pathSlug) {
            $slug = $this->importSlug($pathSlug);
            if (Vehicle::query()->where('slug', $slug)->exists()) {
                continue;
            }

            $url = self::BASE.self::LISTINGS_PREFIX.$pathSlug.'/';
            $response = Http::timeout(self::HTTP_TIMEOUT_SEC)
                ->withHeaders([
                    'User-Agent' => 'MyAutoTorqueImporter/1.0 (+https://example.com)',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get($url);

            if (! $response->successful()) {
                Log::warning('Stylemix sold import: detail page failed', ['url' => $url, 'status' => $response->status()]);

                continue;
            }

            $body = $response->body();

            if (! $this->htmlLooksSoldListing($body)) {
                Log::info('Stylemix sold import: skipped non-sold or ambiguous listing markup', ['url' => $url]);

                continue;
            }

            $parsed = $this->parseListingHtml($body, $url);
            if ($parsed === null) {
                Log::warning('Stylemix sold import: could not parse listing', ['url' => $url]);

                continue;
            }

            DB::transaction(function () use ($parsed, $admin, $slug, $hasShowFinancing): void {
                $attributes = array_merge($parsed['vehicle'], [
                    'user_id' => $admin->id,
                    'slug' => $slug,
                    'status' => 'approved',
                    'submitted_at' => now(),
                    'approved_at' => now(),
                    'approved_by' => $admin->id,
                    'rejection_reason' => null,
                ]);

                if ($hasShowFinancing) {
                    $attributes['show_financing_calculator'] = (bool) ($parsed['show_financing_calculator'] ?? false);
                }

                $vehicle = new Vehicle;
                $vehicle->forceFill($attributes);
                $vehicle->save();

                $sort = 0;
                foreach ($parsed['image_urls'] as $imageUrl) {
                    $sort++;
                    $storedPath = $this->downloadListingImage($imageUrl, $vehicle->id);
                    if ($storedPath === null) {
                        continue;
                    }
                    VehicleImage::query()->create([
                        'vehicle_id' => $vehicle->id,
                        'path' => 'storage/'.$storedPath,
                        'sort_order' => $sort,
                    ]);
                }
            });

            usleep(150_000);
        }
    }

    public function down(): void
    {
        $vehicles = Vehicle::query()
            ->where('slug', 'like', self::SLUG_PREFIX.'%')
            ->with('images')
            ->get();

        foreach ($vehicles as $vehicle) {
            foreach ($vehicle->images as $image) {
                $this->deleteLocalImageIfAny($image->path);
            }
            $vehicle->images()->delete();
            $vehicle->delete();
        }
    }

    private function shouldRunImport(): bool
    {
        // Default OFF everywhere. This migration is from the legacy Auto Torque project and is
        // only relevant for that car dealership demo. Opt in explicitly with IMPORT_STYLEMIX_SOLD_LISTINGS=true.
        return filter_var(env('IMPORT_STYLEMIX_SOLD_LISTINGS', false), FILTER_VALIDATE_BOOL);
    }

    /**
     * @return list<string>
     */
    private function discoverSoldListingPathSlugs(): array
    {
        $seen = [];
        $page = 1;
        $emptyStreak = 0;

        while ($page <= self::MAX_INVENTORY_PAGES) {
            $url = $page === 1
                ? self::BASE.self::INVENTORY_PATH.'?listing_status=sold'
                : self::BASE.self::INVENTORY_PATH.'page/'.$page.'/?listing_status=sold';

            $response = Http::timeout(self::HTTP_TIMEOUT_SEC)
                ->withHeaders([
                    'User-Agent' => 'MyAutoTorqueImporter/1.0 (+https://example.com)',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get($url);

            if (! $response->successful()) {
                Log::warning('Stylemix sold import: inventory page failed', ['url' => $url, 'status' => $response->status()]);
                break;
            }

            $slugs = $this->extractListingPathSlugsFromHtml($response->body());
            $before = count($seen);
            foreach ($slugs as $s) {
                $seen[$s] = true;
            }
            if (count($seen) === $before) {
                $emptyStreak++;
                if ($emptyStreak >= 2) {
                    break;
                }
            } else {
                $emptyStreak = 0;
            }

            $page++;
            usleep(120_000);
        }

        return array_keys($seen);
    }

    /**
     * @return list<string>
     */
    private function extractListingPathSlugsFromHtml(string $html): array
    {
        $soldAnchored = $this->soldAnchoredListingSlugs($html);
        $scope = $this->inventoryListingHtmlScope($html);

        $pattern = '#'.preg_quote(self::LISTINGS_PREFIX, '#').'([^/"\']+)/?#i';
        $count = preg_match_all($pattern, $scope, $m);
        if ($count === false || $count === 0) {
            return array_keys($soldAnchored);
        }

        $candidates = [];
        foreach ($m[1] as $segment) {
            $segment = trim((string) $segment);
            if ($segment === '' || str_contains(strtolower($segment), 'http') || str_contains($segment, '..')) {
                continue;
            }
            $candidates[$segment] = true;
        }

        $out = [];
        foreach (array_keys($candidates) as $segment) {
            if ($soldAnchored !== [] && ! isset($soldAnchored[$segment])) {
                continue;
            }
            $out[$segment] = true;
        }

        if ($out === [] && $candidates !== [] && $soldAnchored !== []) {
            Log::warning('Stylemix sold import: sold-anchor hints did not intersect listing grid links; falling back to inventory-scoped links only.');

            return array_keys($candidates);
        }

        return array_keys($out);
    }

    private function inventoryListingHtmlScope(string $html): string
    {
        foreach ([
            'VEHICLES FOR SALE',
            'stm-dynamic-listing-filter',
            'stm-isotope-sorting',
            'stm-inventory-pro',
            '<article',
        ] as $needle) {
            $pos = stripos($html, $needle);
            if ($pos !== false) {
                return substr($html, $pos, 400000);
            }
        }

        return $html;
    }

    /**
     * Prefer slugs anchored near explicit "sold" cues to avoid mega-menu listing links polluting crawl results.
     *
     * @return array<string, true>
     */
    private function soldAnchoredListingSlugs(string $html): array
    {
        $out = [];

        // Example pattern: "... Sold ... href=""/elementor-dealer-two/listings/{slug}/""
        if (preg_match_all(
            '#\bSold\b.{0,5000}?'.preg_quote(self::LISTINGS_PREFIX, '#').'([^/"\']+)/?#is',
            $html,
            $m
        ) && isset($m[1])) {
            foreach ($m[1] as $segment) {
                $segment = trim((string) $segment);
                if ($segment !== '' && ! str_contains($segment, '..')) {
                    $out[$segment] = true;
                }
            }
        }

        // Common Motors / WP cues
        $cues = [
            'listing-status-sold',
            'sold-label',
            'stm-label-sold',
            'sold_listing_label',
            'sold_listing_icon',
            'sold_listing/',
        ];

        foreach ($cues as $needle) {
            if (! str_contains(strtolower($html), strtolower((string) $needle))) {
                continue;
            }
            if (
                preg_match_all(
                    '#'.preg_quote(self::LISTINGS_PREFIX, '#').'([^/"\']+)/?#i',
                    substr($html, max(0, stripos($html, $needle) - 2500), 10000),
                    $m
                )
                && isset($m[1])
            ) {
                foreach ($m[1] as $segment) {
                    $segment = trim((string) $segment);
                    if ($segment !== '') {
                        $out[$segment] = true;
                    }
                }
            }
        }

        return $out;
    }

    private function htmlLooksSoldListing(string $html): bool
    {
        $lower = strtolower($html);

        if (
            str_contains($lower, 'listing-status-sold')
            || str_contains($lower, 'stm-label-sold')
            || str_contains($lower, 'sold_listing')
            || str_contains($lower, 'stm-badge-sold')
            || preg_match('#\bclass=(["\'])[^"\']*\bsold\b[^"\']*\1#i', $html) === 1
        ) {
            return true;
        }

        $h1 = stripos($html, '<h1');
        if ($h1 !== false) {
            $slice = substr($html, $h1, 25_000);
            if (preg_match('#\bSold\b#', strip_tags($slice)) === 1) {
                return true;
            }
        }

        return false;
    }

    private function importSlug(string $pathSlug): string
    {
        return Str::limit(self::SLUG_PREFIX.$pathSlug, 255, '');
    }

    /**
     * @return array{vehicle: array<string, mixed>, image_urls: list<string>, show_financing_calculator?: bool}|null
     */
    private function parseListingHtml(string $html, string $sourceUrl): ?array
    {
        $title = $this->metaContent($html, 'property', 'og:title')
            ?: $this->metaContent($html, 'name', 'twitter:title')
            ?: $this->firstTagText($html, 'h1');

        $title = $this->cleanTitle($title);
        if ($title === '') {
            return null;
        }

        $specs = $this->parseSpecTable($html);
        $yearUpper = (int) date('Y') + 1;
        $year = isset($specs['year']) ? (int) $specs['year'] : null;
        if ($year !== null && ($year < 1900 || $year > $yearUpper)) {
            $year = null;
        }
        $makeModel = $this->inferMakeModel($title, $year);

        $prices = $this->parsePrices($html);
        $mileage = isset($specs['mileage']) ? $this->parseIntish($specs['mileage']) : null;
        $mpg = $this->parseCityHwyMpg($html);

        $overview = $this->extractOverviewText($html);
        $features = $this->extractFeatureLines($html);
        $techSpecs = $this->buildTechSpecs($html, $specs);

        $contact = $this->parseContactBlock($html);

        $imageUrls = $this->collectGalleryImageUrls($html, $sourceUrl);

        $bodyType = $specs['body'] ?? null;
        if (is_string($bodyType) && str_contains($bodyType, ',')) {
            $bodyType = trim(explode(',', $bodyType, 2)[0]);
        }

        $vehicle = [
            'title' => Str::limit($title, 255, ''),
            'year' => $year,
            'make' => $makeModel['make'],
            'model' => $makeModel['model'],
            'price' => $prices['price'],
            'msrp' => $prices['msrp'],
            'mileage' => $mileage,
            'city_mpg' => $mpg['city'],
            'hwy_mpg' => $mpg['hwy'],
            'transmission' => $this->nullableString($specs['transmission'] ?? null, 255),
            'fuel_type' => $this->nullableString($specs['fuel type'] ?? $specs['fuel_type'] ?? null, 255),
            'drive' => $this->nullableString($specs['drive'] ?? null, 255),
            'body_type' => $this->nullableString($bodyType, 255),
            'condition' => 'used',
            'engine_size' => $this->nullableString($specs['engine'] ?? null, 64),
            'location' => $this->nullableString($contact['location'] ?? null, 255),
            'contact_phone' => $this->nullableString($contact['phone'] ?? null, 64),
            'contact_address' => $this->nullableString($contact['address'] ?? null, 255),
            'contact_email' => $this->nullableString($contact['email'] ?? null, 255),
            'map_location' => null,
            'features' => $features,
            'exterior_color' => $this->nullableString($specs['exterior color'] ?? $specs['exterior_color'] ?? null, 255),
            'interior_color' => $this->nullableString($specs['interior color'] ?? $specs['interior_color'] ?? null, 255),
            'vin' => $this->nullableString($specs['vin'] ?? null, 255),
            'video_url' => null,
            'description' => $overview !== '' ? $overview : 'Imported from Stylemix Motors demo inventory.',
            'overview' => $overview !== '' ? $overview : null,
            'tech_specs' => $techSpecs,
            'finance_price' => null,
            'finance_interest_rate' => null,
            'finance_term_months' => null,
            'finance_down_payment' => null,
            'is_special' => false,
        ];

        $engineLayout = is_array($techSpecs) ? ($techSpecs['engine_layout'] ?? null) : null;
        if (is_string($engineLayout) && $engineLayout !== '') {
            $vehicle['engine_layout'] = Str::limit($engineLayout, 100, '');
        }

        return [
            'vehicle' => $vehicle,
            'image_urls' => $imageUrls,
            'show_financing_calculator' => str_contains(strtolower($html), 'financing calculator'),
        ];
    }

    private function cleanTitle(string $title): string
    {
        $title = html_entity_decode(trim($title), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $title = preg_replace('/\s+–\s+.*$/u', '', $title) ?? $title;
        $title = preg_replace('/\s+-\s+Car Dealership.*$/i', '', $title) ?? $title;

        return trim($title);
    }

    /**
     * @return array<string, string>
     */
    private function parseSpecTable(string $html): array
    {
        $specs = [];
        libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $wrapped = '<?xml encoding="UTF-8">'.$html;
        if (@$dom->loadHTML($wrapped, LIBXML_NOERROR | LIBXML_NOWARNING) !== true) {
            return $specs;
        }

        $xpath = new DOMXPath($dom);
        foreach ($xpath->query('//table//tr') as $tr) {
            if (! $tr instanceof DOMElement) {
                continue;
            }
            $cells = $tr->getElementsByTagName('td');
            if ($cells->length < 2) {
                continue;
            }
            $label = $this->normalizeSpecLabel((string) $cells->item(0)?->textContent);
            $value = trim(preg_replace('/\s+/', ' ', (string) $cells->item(1)?->textContent) ?? '');
            if ($label !== '' && $value !== '') {
                $specs[$label] = $value;
            }
        }

        // dt/dd fallback
        foreach ($xpath->query('//dl') as $dl) {
            if (! $dl instanceof DOMElement) {
                continue;
            }
            $dts = $dl->getElementsByTagName('dt');
            $dds = $dl->getElementsByTagName('dd');
            $n = min($dts->length, $dds->length);
            for ($i = 0; $i < $n; $i++) {
                $label = $this->normalizeSpecLabel((string) $dts->item($i)?->textContent);
                $value = trim(preg_replace('/\s+/', ' ', (string) $dds->item($i)?->textContent) ?? '');
                if ($label !== '' && $value !== '') {
                    $specs[$label] = $value;
                }
            }
        }

        libxml_clear_errors();

        return $specs;
    }

    private function normalizeSpecLabel(string $label): string
    {
        $label = html_entity_decode(trim($label), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $label = strtolower($label);
        $label = rtrim($label, ':');

        return trim($label);
    }

    /**
     * @return array{price: int|null, msrp: int|null}
     */
    private function parsePrices(string $html): array
    {
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        $msrp = null;
        if (preg_match('/MSRP\s*\$?\s*([\d\s,\.]+)/i', $text, $m)) {
            $msrp = $this->moneyToInt($m[1]);
        }

        $newPrice = null;
        if (preg_match('/New Price\s*\$?\s*([\d\s,\.]+)/i', $text, $m)) {
            $newPrice = $this->moneyToInt($m[1]);
        }

        $ourPrice = null;
        if (preg_match('/Our price\s*\$?\s*([\d\s,\.]+)/i', $text, $m)) {
            $ourPrice = $this->moneyToInt($m[1]);
        }

        $buyFor = null;
        if (preg_match('/Buy for\s*\$?\s*([\d\s,\.]+)/i', $text, $m)) {
            $buyFor = $this->moneyToInt($m[1]);
        }

        $oldPrice = null;
        if (preg_match('/Old Price\s*\$?\s*([\d\s,\.]+)/i', $text, $m)) {
            $oldPrice = $this->moneyToInt($m[1]);
        }

        $price = $newPrice ?? $ourPrice ?? $buyFor;
        if ($price === null && $oldPrice !== null) {
            $price = $oldPrice;
        }

        // If we have Old and New: treat New as price, Old as strike/MSRP when MSRP missing
        if ($msrp === null && $oldPrice !== null && $newPrice !== null && $oldPrice !== $newPrice) {
            $msrp = $oldPrice;
            $price = $newPrice;
        }

        if ($price === null && $msrp !== null) {
            $price = $msrp;
            $msrp = null;
        }

        return ['price' => $price, 'msrp' => $msrp];
    }

    private function moneyToInt(string $raw): ?int
    {
        $digits = preg_replace('/[^\d]/', '', $raw) ?? '';
        if ($digits === '') {
            return null;
        }

        return (int) $digits;
    }

    /**
     * @return array{city: int|null, hwy: int|null}
     */
    private function parseCityHwyMpg(string $html): array
    {
        $plain = strtolower(strip_tags($html));
        $plain = preg_replace('/\s+/', ' ', $plain) ?? $plain;

        $city = null;
        $hwy = null;
        if (preg_match('/(\d+)\s*city mpg/i', $plain, $m)) {
            $city = min(200, max(0, (int) $m[1]));
        }
        if (preg_match('/(\d+)\s*hwy mpg/i', $plain, $m)) {
            $hwy = min(200, max(0, (int) $m[1]));
        }

        return ['city' => $city, 'hwy' => $hwy];
    }

    private function parseIntish(string $value): ?int
    {
        $digits = preg_replace('/[^\d]/', '', $value) ?? '';

        return $digits === '' ? null : (int) $digits;
    }

    /**
     * @return array{make: ?string, model: ?string}
     */
    private function inferMakeModel(string $title, ?int $year): array
    {
        $t = preg_replace('/\s+/', ' ', trim($title));
        $words = preg_split('/\s+/', $t) ?: [];
        if ($words === []) {
            return ['make' => null, 'model' => null];
        }

        $last = end($words);
        if (is_string($last) && preg_match('/^\d{4}$/', $last)) {
            array_pop($words);
        }
        if ($year !== null) {
            $words = array_values(array_filter($words, fn ($w) => (string) $w !== (string) $year));
        }

        if ($words === []) {
            return ['make' => null, 'model' => null];
        }

        $make = array_shift($words);
        $make = is_string($make) ? trim($make) : null;

        $makeOut = is_string($make) && trim($make) !== '' ? Str::limit(trim($make), 255, '') : null;
        $modelOut = $words !== [] ? Str::limit(implode(' ', $words), 255, '') : null;
        $modelOut = $modelOut !== null && trim($modelOut) === '' ? null : $modelOut;

        return [
            'make' => $makeOut,
            'model' => $modelOut,
        ];
    }

    private function extractOverviewText(string $html): string
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $wrapped = '<?xml encoding="UTF-8">'.$html;
        if (@$dom->loadHTML($wrapped, LIBXML_NOERROR | LIBXML_NOWARNING) !== true) {
            return '';
        }

        $xpath = new DOMXPath($dom);

        $chunks = [];

        foreach ($xpath->query('//h3|//h2') ?: [] as $heading) {
            if (! $heading instanceof DOMElement) {
                continue;
            }
            $headingText = strtolower(trim($heading->textContent));
            if (! str_contains($headingText, 'vehicle overview')) {
                continue;
            }

            $sibling = $heading->nextSibling;
            $guard = 0;
            while ($sibling !== null && $guard < 40) {
                $guard++;

                if ($sibling instanceof DOMElement) {
                    $tag = strtolower($sibling->tagName ?? '');
                    if (in_array($tag, ['h2', 'h3', 'h4'], true)) {
                        $nextText = strtolower(trim($sibling->textContent));
                        // Stop at subsequent major listing sections commonly present on demos
                        if (str_contains($nextText, 'features') || str_contains($nextText, 'gallery') || str_contains($nextText, 'location')) {
                            break;
                        }
                    }

                    if (in_array($tag, ['p', 'div', 'article', 'section'], true)) {
                        $txt = trim($sibling->textContent);
                        if ($txt !== '') {
                            $chunks[] = $txt;
                        }
                    }
                }

                $sibling = $sibling->nextSibling;
            }
        }

        libxml_clear_errors();

        $out = trim(implode("\n\n", array_values(array_unique($chunks))));

        return Str::limit($out, 50_000, '');
    }

    /**
     * @return array<int, string>|null
     */
    private function extractFeatureLines(string $html): ?array
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $wrapped = '<?xml encoding="UTF-8">'.$html;
        if (@$dom->loadHTML($wrapped, LIBXML_NOERROR | LIBXML_NOWARNING) !== true) {
            libxml_clear_errors();

            return null;
        }

        $xpath = new DOMXPath($dom);
        $picked = [];

        foreach ($xpath->query('//ul//li') ?: [] as $li) {
            $txt = trim(preg_replace('/\s+/', ' ', $li->textContent) ?? '');
            if ($txt === '' || strlen($txt) > 240) {
                continue;
            }
            // Exclude obvious nav/contact junk
            if (preg_match('/^(home|inventory|contacts|loan|leasing|instagram|facebook)/i', $txt) === 1) {
                continue;
            }
            if (preg_match('/\$\s*\d/i', $txt) === 1) {
                continue;
            }

            // Prefer list items that resemble feature bullets on listing pages
            if (preg_match('/\b(abs|esp|bluetooth|leather|sunroof|navi|airbags|heated|awd|rwd|fwd)\b/i', $txt) === 1 || strlen($txt) <= 96) {
                $picked[$txt] = true;
                if (count($picked) >= 40) {
                    break;
                }
            }
        }

        libxml_clear_errors();

        if ($picked === []) {
            return null;
        }

        return array_values(array_keys(array_slice($picked, 0, 40)));
    }

    /**
     * @param  array<string, string>  $specs
     * @return array<string, string>|null
     */
    private function buildTechSpecs(string $html, array $specs): ?array
    {
        unset($specs);

        $plain = strtolower($html);

        // Pull common Motors-ish blocks via lightweight regex scans on plain text-ish parts.
        $text = strtolower(strip_tags($html));
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        $engineLayout = $this->firstMatchCapture($plain, '#layout\s*v\s*\d+#i')
            ?: $this->firstMatchCapture($text, '#layout\s*v\s*\d+#i');

        $engineVol = $this->firstMatchCapture($text, '#engine volume\s*([0-9][0-9\.\s]*\s*[l]*)#i');

        $driveType = $this->firstMatchCapture($text, '#type of drive\s*([a-z0-9]+)#i');

        $topSpeed = $this->firstMatchCapture($text, '#top track speed\s*([0-9][0-9\.\s]*\s*mph)#i');

        $zero = $this->firstMatchCapture($text, '#0\s*-\s*70\s*mph\s*([0-9][0-9\.\s]*\s*s)#i');

        $gears = $this->firstMatchCapture($text, '#number of gears\s*([0-9]+)#i');

        $mapped = [];
        if (is_string($engineLayout)) {
            $mapped['engine_layout'] = Str::limit(trim(Str::upper($engineLayout)), 100, '');
        }
        if (is_string($engineVol)) {
            $mapped['engine_volume'] = Str::limit(trim($engineVol), 100, '');
        }
        if (is_string($driveType)) {
            $mapped['drive_type'] = Str::limit(strtoupper(trim($driveType)), 100, '');
        }
        if (is_string($topSpeed)) {
            $mapped['top_speed'] = Str::limit(trim($topSpeed), 100, '');
        }
        if (is_string($zero)) {
            $mapped['zero_to_70'] = Str::limit(trim($zero), 100, '');
        }
        if (is_string($gears)) {
            $mapped['transmission_gears'] = Str::limit(trim($gears), 100, '');
        }

        return $mapped === [] ? null : $mapped;
    }

    private function firstMatchCapture(string $haystack, string $pattern): ?string
    {
        if (preg_match($pattern, $haystack, $m) === 1) {
            return trim((string) ($m[1] ?? $m[0]));
        }

        return null;
    }

    /**
     * @return array{phone: ?string, email: ?string, address: ?string, location: ?string}
     */
    private function parseContactBlock(string $html): array
    {
        $plain = strip_tags($html);
        $plain = preg_replace('/\s+/', ' ', $plain) ?? $plain;

        $phone = null;
        if (preg_match('/PHONE:?\s*([+\d\s\-\(\)]+)/i', $plain, $m)) {
            $phone = preg_replace('/\s+/', ' ', trim($m[1])) ?? null;
            $phone = $phone !== null ? Str::limit($phone, 64, '') : null;
        }

        $address = null;
        if (preg_match('/(\d+\s+[A-Za-z0-9\.\,\s]+\b(?:CA|NY|TX|FL|USA)\b[^.\n\r]{0,120})/i', $plain, $m)) {
            $address = Str::limit(trim($m[1]), 255, '');
        }

        return [
            'phone' => $phone,
            'email' => null,
            'address' => $address,
            'location' => $address,
        ];
    }

    /**
     * @return list<string>
     */
    private function collectGalleryImageUrls(string $html, string $sourceUrl): array
    {
        $urls = [];

        preg_match_all('#(?:src|data-src)=["\']([^"\']+\.(?:jpe?g|png|webp)(?:\?[^"\']*)?)#i', $html, $m);
        foreach ($m[1] ?? [] as $raw) {
            $u = trim((string) $raw);
            if ($u === '') {
                continue;
            }

            // Skip tiny icons sprites
            if (preg_match('/(icon|logo|sprite|emoji|grey\.png|transparent)/i', $u) === 1) {
                continue;
            }

            if ($this->looksLikeListingPhotoUrl($u)) {
                $urls[$this->normalizeImageUrl($u, $sourceUrl)] = true;
            }
        }

        // og:image fallback
        $og = $this->metaContent($html, 'property', 'og:image');
        if (is_string($og) && $og !== '') {
            $urls[$this->normalizeImageUrl($og, $sourceUrl)] = true;
        }

        return array_slice(array_keys($urls), 0, 12);
    }

    private function looksLikeListingPhotoUrl(string $url): bool
    {
        $lower = strtolower($url);

        return str_contains($lower, '/wp-content/uploads/')
            || str_contains($lower, '/uploads/')
            || str_contains($lower, '.stylemixthemes.com');
    }

    private function normalizeImageUrl(string $url, string $_baseListingUrl): string
    {
        $url = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $url = str_replace('&amp;', '&', $url);
        if (str_starts_with($url, '//')) {
            return 'https:'.$url;
        }
        if (str_starts_with($url, '/')) {
            return self::BASE.$url;
        }

        return $url;
    }

    private function metaContent(string $html, string $attrName, string $attrValue): string
    {
        $name = preg_quote($attrName, '#');
        $val = preg_quote($attrValue, '#');

        if (preg_match(
            '#<meta[^>]*\b'.$name.'=(["\'])'.$val.'\1[^>]*\bcontent=(["\'])(.*?)\2#is',
            $html,
            $m
        ) === 1) {
            return html_entity_decode(trim($m[3]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        if (preg_match(
            '#<meta[^>]*\bcontent=(["\'])(.*?)\1[^>]*\b'.$name.'=(["\'])'.$val.'\3#is',
            $html,
            $m
        ) === 1) {
            return html_entity_decode(trim($m[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return '';
    }

    private function firstTagText(string $html, string $tag): string
    {
        if (preg_match('#<'.$tag.'[^>]*>(.*?)</'.$tag.'>#is', $html, $m) === 1) {
            return trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        return '';
    }

    private function nullableString(?string $value, int $limit): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($value === '') {
            return null;
        }

        return Str::limit($value, $limit, '');
    }

    private function downloadListingImage(string $absoluteUrl, int $vehicleId): ?string
    {
        try {
            $resp = Http::timeout(self::HTTP_TIMEOUT_SEC)
                ->withHeaders([
                    'User-Agent' => 'MyAutoTorqueImporter/1.0 (+https://example.com)',
                    'Accept' => 'image/*,*/*',
                ])
                ->get($absoluteUrl);

            if (! $resp->successful()) {
                return null;
            }

            $body = $resp->body();
            if ($body === '') {
                return null;
            }

            if (! $this->isProbablyImageBinary($body)) {
                return null;
            }

            $ext = $this->guessImageExtensionFromResponse($resp->header('Content-Type'), $absoluteUrl);
            $relativeDir = trim(config('media.listing_photos_directory', 'listings/vehicles'), '/').'/'.$vehicleId;
            $filename = (string) Str::uuid().'.'.$ext;
            Storage::disk('public')->put($relativeDir.'/'.$filename, $body, ['visibility' => 'public']);

            return $relativeDir.'/'.$filename;
        } catch (Throwable $e) {
            Log::info('Stylemix sold import: image download skipped', ['url' => $absoluteUrl, 'message' => $e->getMessage()]);

            return null;
        }
    }

    private function isProbablyImageBinary(string $body): bool
    {
        if (strlen($body) < 12) {
            return false;
        }

        if (str_starts_with($body, "\xFF\xD8\xFF")) {
            return true;
        }
        if (str_starts_with($body, "\x89PNG\r\n\x1a\n")) {
            return true;
        }
        if (str_starts_with($body, 'GIF8')) {
            return true;
        }

        // WEBP starts with RIFF....WEBP
        return str_starts_with($body, 'RIFF') && substr($body, 8, 4) === 'WEBP';
    }

    private function guessImageExtensionFromResponse(?string $contentType, string $absoluteUrl): string
    {
        $mime = strtolower((string) $contentType);
        if (str_contains($mime, 'png')) {
            return 'png';
        }
        if (str_contains($mime, 'webp')) {
            return 'webp';
        }
        if (str_contains($mime, 'gif')) {
            return 'gif';
        }
        if (str_contains($mime, 'jpeg') || str_contains($mime, 'jpg')) {
            return 'jpg';
        }

        $lower = strtolower($absoluteUrl);
        foreach (['.webp', '.png', '.gif', '.jpg', '.jpeg'] as $needle) {
            if (str_contains($lower, $needle)) {
                return match ($needle) {
                    '.png' => 'png',
                    '.webp' => 'webp',
                    '.gif' => 'gif',
                    default => 'jpg',
                };
            }
        }

        return 'jpg';
    }

    private function deleteLocalImageIfAny(?string $publicPath): void
    {
        if ($publicPath === null || $publicPath === '') {
            return;
        }

        if (preg_match('#^https?://#i', $publicPath) === 1) {
            return;
        }

        $rel = ltrim(Str::after($publicPath, 'storage/'), '/');
        if ($rel !== '' && Storage::disk('public')->exists($rel)) {
            Storage::disk('public')->delete($rel);
        }
    }
};
