<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Models\Vehicle;
use App\Models\VehicleVariant;
use App\Support\Compare;
use App\Support\SiteBrand;
use App\Support\SiteSettingDefaults;
use App\Support\VehicleImageUrl;
use App\Support\VehicleListingCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PageController extends Controller
{
    /**
     * @param  array<string, string>  $defaults
     * @return array<string, string>
     */
    protected function pageSections(string $slug, array $defaults): array
    {
        $stored = PageSection::query()
            ->where('page', $slug)
            ->pluck('content', 'section_key');
        $out = [];
        foreach ($defaults as $key => $default) {
            $out[$key] = (string) ($stored[$key] ?? SiteSetting::getValue('page_'.$slug.'_'.$key, $default) ?? $default);
        }

        return $out;
    }

    public function home()
    {
        $page = CmsPage::query()->where('slug', 'home')->where('is_active', true)->firstOrFail();
        $siteName = SiteBrand::displayName();
        $recentVehicles = Vehicle::query()
            ->with(['images', 'categoryOption'])
            ->where('status', 'approved')
            // Homepage "recent products" should reflect newly approved/added inventory,
            // not last edited listings.
            ->orderByDesc('approved_at')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        $filterOptions = $this->approvedVehicleFilterOptions();
        $filters = $this->defaultInventoryFilters();

        $heroCover = $recentVehicles->first()?->images->first();
        $ogImage = $heroCover ? url(VehicleImageUrl::url($heroCover->path)) : null;

        $homeSections = $this->pageSections('home', [
            'hero_title' => 'Discover Your Signature Style',
            'hero_subtitle' => 'Considered apparel for the modern wardrobe',
            'hero_description' => '',
            'hero_cta_text' => 'Explore the Collection',
            'hero_cta_href' => '/shop',
            'home_search_label' => 'Search the collection',
            'recent_title' => 'New Arrivals',
            'recent_subtitle' => 'Fresh styles, just dropped. Cards below are live listings.',
            'hero_image' => 'asset/images/media/home-hero-main.jpg',
            'dealer_cta_bg' => 'asset/images/media/home-cta-left.jpg',
            'dealer_cta_left_icon' => 'shopping_bag',
            'dealer_cta_right_icon' => 'storefront',
            'cta_left_title' => 'Shop the look',
            'cta_left_body' => 'Curated pieces delivered to your door. Free returns within 30 days.',
            'cta_left_button_text' => 'Shop now',
            'cta_left_button_href' => '/shop',
            'cta_right_title' => 'Visit the atelier',
            'cta_right_body' => 'Book a private session with our stylists for fittings and personal styling.',
            'cta_right_button_text' => 'Book a visit',
            'cta_right_button_href' => '/contact',
            'feat1_title' => 'Considered design',
            'feat1_body' => 'Pieces designed in small batches with attention to fabric, fit, and finishing.',
            'feat2_title' => 'Quality fabrics',
            'feat2_body' => 'Sustainably sourced materials chosen for longevity, not seasons.',
            'feat3_title' => 'Effortless returns',
            'feat3_body' => 'Try on at home with free 30-day returns. No questions asked.',
            'welcome_title' => 'A wardrobe worth keeping',
            'welcome_body' => 'Considered apparel made to last. Built for the modern wardrobe.',
            'welcome_video_url' => '',
            'prefooter_title' => 'Have a question?',
            'prefooter_button_text' => 'Contact',
            'prefooter_button_href' => '/contact',
        ]);

        return view('pages.home-luxemotive', [
            'title' => $page->title.' | '.$siteName,
            'metaDescription' => $page->meta_description,
            'canonicalUrl' => route('home', [], true),
            'ogTitle' => $page->title,
            'ogDescription' => $page->meta_description,
            'ogUrl' => route('home', [], true),
            'ogImage' => $ogImage,
            'page' => $page,
            'heroVehicle' => $recentVehicles->first(),
            'recentVehicles' => $recentVehicles,
            'filterOptions' => $filterOptions,
            'filters' => $filters,
            'dealerPhone' => SiteSetting::getValue('dealer_phone', '+1 878-9674-4455'),
            'approvedListingCount' => Vehicle::query()->where('status', 'approved')->count(),
            'sections' => $homeSections,
        ]);
    }

    public function about()
    {
        $page = CmsPage::query()->where('slug', 'about')->where('is_active', true)->firstOrFail();
        $siteName = SiteBrand::displayName();
        $sections = $this->pageSections('about', [
            'hero_image' => 'asset/images/media/about-hero-bg.jpg',
            'established_year' => '2020',
            'hero_stat_value' => '5+',
            'hero_stat_label' => 'Years of Craft',
            'heading' => 'WELCOME TO THE ATELIER',
            'intro' => 'A small studio creating considered apparel for the modern wardrobe. Every piece is designed to last and made in small batches.',
            'hero_primary_cta_text' => 'Our Story',
            'hero_primary_cta_href' => '/about',

            'values_title' => 'CORE VALUES',
            'val_1_title' => 'Considered design',
            'val_1_body' => 'Every silhouette is drafted, sampled, and refined before it ships.',
            'val_2_title' => 'Quality fabric',
            'val_2_body' => 'Sourced from mills with traceable supply chains and high finishing standards.',
            'val_3_title' => 'Customer care',
            'val_3_body' => 'Real humans answer questions, manage returns, and remember repeat customers.',
            'values_grid_1' => 'asset/images/media/about-values-1.jpg',
            'values_grid_2' => 'asset/images/media/about-values-2.jpg',
            'values_grid_3' => 'asset/images/media/about-values-3.jpg',
            'values_grid_4' => 'asset/images/media/about-values-4.jpg',

            'gallery_title' => 'Media Gallery',
            'gallery' => '[]',

            'advantages_title' => 'Quick Links',
            'adv_1_icon' => 'shopping_bag',
            'adv_1_title' => 'Shop the collection',
            'adv_1_body' => 'Browse the latest drops in our online store.',
            'adv_1_href' => '/shop',
            'adv_2_icon' => 'storefront',
            'adv_2_title' => 'Visit the atelier',
            'adv_2_body' => 'Book a styling session for personal advice and fittings.',
            'adv_2_href' => '/contact',
            'adv_3_icon' => 'mail',
            'adv_3_title' => 'Stay in the loop',
            'adv_3_body' => 'Subscribe for new arrivals and behind-the-scenes notes.',
            'adv_3_href' => '/contact',

            'testimonials_title' => 'Customer Notes',
            'testimonial_1_body' => 'The fit was spot on and the fabric feels remarkable. My new go-to.',
            'testimonial_1_author' => 'Jane Doe',
            'testimonial_1_brand' => 'Repeat Customer',
        ]);

        return view('pages.about', [
            'title' => $page->title.' | '.$siteName,
            'metaDescription' => $page->meta_description,
            'canonicalUrl' => route('about', [], true),
            'ogTitle' => $page->title,
            'ogDescription' => $page->meta_description,
            'ogUrl' => route('about', [], true),
            'page' => $page,
            'sections' => $sections,
        ]);
    }

    public function contact()
    {
        $page = CmsPage::query()->where('slug', 'contact')->where('is_active', true)->firstOrFail();
        $siteName = SiteBrand::displayName();

        return view('pages.contact', [
            'title' => $page->title.' | '.$siteName,
            'metaDescription' => $page->meta_description,
            'canonicalUrl' => route('contact', [], true),
            'ogTitle' => $page->title,
            'ogDescription' => $page->meta_description,
            'ogUrl' => route('contact', [], true),
            'page' => $page,
            'sections' => $this->pageSections('contact', [
                'heading' => 'Contact Us',
                'intro' => 'Reach our team using the form below.',
                'hero_image' => 'asset/images/media/contact-hero-bg.jpg',
                'map_address' => '',
                'map_embed_url' => '',
                'map_fallback_image' => 'asset/images/media/contact-map.jpg',
                'sales_address' => SiteSetting::getValue('dealer_address', ''),
                'sales_phone' => SiteSetting::getValue('dealer_sales_phone', ''),
                'sales_hours' => SiteSetting::getValue('dealer_sales_hours', "Mon - Fri: 09:00AM - 06:00PM\nSaturday: 10:00AM - 04:00PM\nSunday: Closed"),
                'parts_address' => SiteSetting::getValue('dealer_address', ''),
                'parts_phone' => '',
                'parts_hours' => "Mon - Fri: 09:00AM - 06:00PM\nSaturday: 10:00AM - 04:00PM\nSunday: Closed",
                'renting_address' => SiteSetting::getValue('dealer_address', ''),
                'renting_phone' => '',
                'renting_hours' => "Mon - Fri: 09:00AM - 06:00PM\nSaturday: 10:00AM - 04:00PM\nSunday: Closed",
            ]),
        ]);
    }

    public function faq()
    {
        $page = CmsPage::query()->where('slug', 'faq')->where('is_active', true)->firstOrFail();
        $siteName = SiteBrand::displayName();
        $sections = $this->pageSections('faq', [
            'kicker' => 'Need Help?',
            'heading' => 'HELP CENTER',
            'intro' => 'Everything you need to know about ordering, shipping, and returns.',
            'hero_image' => 'asset/images/media/faq-hero-bg.jpg',
            'cat_1_title' => 'Ordering',
            'cat_1_icon' => 'shopping_bag',
            'cat_1_faqs' => json_encode([
                ['q' => 'How long does processing take?', 'a' => 'Orders are processed within 1-2 business days. You will receive a tracking number once your order ships.'],
                ['q' => 'Can I modify or cancel my order?', 'a' => 'Contact us within 24 hours of placing the order and we will do our best to accommodate changes.'],
                ['q' => 'Do you ship internationally?', 'a' => 'Yes — international shipping is available at checkout for most regions.'],
            ]),
            'cat_2_title' => 'Returns & Exchanges',
            'cat_2_icon' => 'autorenew',
            'cat_2_faqs' => json_encode([
                ['q' => 'What is your return policy?', 'a' => 'Returns are accepted within 30 days of delivery for unworn items in original condition.'],
                ['q' => 'How do I exchange a size?', 'a' => 'Request an exchange via the returns portal — we will ship the new size as soon as we receive the original.'],
            ]),
            'cat_3_title' => 'Fit & Sizing',
            'cat_3_icon' => 'straighten',
            'cat_3_faqs' => json_encode([
                ['q' => 'Where can I find size guides?', 'a' => 'Each product page includes a size guide tab with measurements.'],
            ]),
            'cat_4_title' => 'Care',
            'cat_4_icon' => 'water_drop',
            'cat_4_faqs' => json_encode([
                ['q' => 'How should I care for my pieces?', 'a' => 'Care instructions are printed on each garment and noted on its product page.'],
            ]),
            'cta_title' => 'STILL HAVE QUESTIONS?',
            'cta_body' => 'Our customer care team is available Monday through Saturday.',
            'cta_image' => 'asset/images/media/faq-cta.jpg',
        ]);

        return view('pages.faq', [
            'title' => $page->title.' | '.$siteName,
            'metaDescription' => $page->meta_description,
            'canonicalUrl' => route('faq', [], true),
            'ogTitle' => $page->title,
            'ogDescription' => $page->meta_description,
            'ogUrl' => route('faq', [], true),
            'page' => $page,
            'sections' => $sections,
        ]);
    }

    public function privacyPolicy()
    {
        $page = CmsPage::query()->where('slug', 'privacy-policy')->where('is_active', true)->firstOrFail();
        $siteName = SiteBrand::displayName();
        $sections = $this->pageSections('privacy-policy', [
            'heading' => 'Privacy Policy',
            'body' => "This Privacy Policy explains how we collect, use, and protect information when you use our online store.\n\nIf you create an account, sign in (including via Google), save items, submit inquiries, or contact us, we may process the information you provide to deliver these features.\n\nWe do not sell your personal information. We use your information to operate the site, communicate with you, prevent fraud, and comply with legal obligations.\n\nFor questions about this policy or to request access, correction, or deletion of your data, please use the Contact page.",
        ]);

        return view('pages.legal', [
            'title' => $page->title.' | '.$siteName,
            'metaDescription' => $page->meta_description,
            'canonicalUrl' => route('privacy-policy', [], true),
            'ogTitle' => $page->title,
            'ogDescription' => $page->meta_description,
            'ogUrl' => route('privacy-policy', [], true),
            'page' => $page,
            'sections' => $sections,
        ]);
    }

    public function terms()
    {
        $page = CmsPage::query()->where('slug', 'terms')->where('is_active', true)->firstOrFail();
        $siteName = SiteBrand::displayName();
        $sections = $this->pageSections('terms', [
            'heading' => 'Terms & Conditions',
            'body' => "These Terms & Conditions govern your use of our online store.\n\nBy using the site, you agree not to misuse the platform, attempt unauthorized access, or submit false or misleading information.\n\nProducts may be added by staff accounts. Product details, pricing, and availability can change and are provided for informational purposes.\n\nIf you submit an inquiry, you agree that we may contact you using the information you provide.\n\nWe may suspend or terminate accounts that violate these terms. These terms may be updated from time to time; continued use indicates acceptance of updates.",
        ]);

        return view('pages.legal', [
            'title' => $page->title.' | '.$siteName,
            'metaDescription' => $page->meta_description,
            'canonicalUrl' => route('terms', [], true),
            'ogTitle' => $page->title,
            'ogDescription' => $page->meta_description,
            'ogUrl' => route('terms', [], true),
            'page' => $page,
            'sections' => $sections,
        ]);
    }

    /**
     * Legacy /makes endpoint — now redirects to the shop. A "browse by make" landing
     * page no longer applies to an apparel store.
     */
    public function makesIndex()
    {
        return redirect()->route('shop.index', [], 301);
    }

    public function inventory(Request $request)
    {
        try {
            $page = CmsPage::query()->where('slug', 'inventory')->where('is_active', true)->first();
            $filterOpts = $this->approvedVehicleFilterOptions();

            $idIn = function (Collection $rows): array {
                $ids = $rows->pluck('id')->filter()->values()->all();

                return $ids === [] ? [] : [Rule::in($ids)];
            };

            $filters = $request->validate([
                'q' => ['nullable', 'string', 'max:255'],
                'product_category_listing_option_id' => array_merge(['nullable', 'integer'], $idIn(collect($filterOpts['categories'] ?? []))),
                'size_id' => array_merge(['nullable', 'integer'], $idIn(collect($filterOpts['sizes'] ?? []))),
                'color_id' => array_merge(['nullable', 'integer'], $idIn(collect($filterOpts['colors'] ?? []))),
                'price_min' => ['nullable', 'integer', 'min:0', 'max:999999999'],
                'price_max' => ['nullable', 'integer', 'min:0', 'max:999999999'],
                'sort' => ['nullable', 'string', Rule::in(['newest', 'price_low', 'price_high'])],
            ]);

            if (! empty($filters['price_min']) && ! empty($filters['price_max']) && (int) $filters['price_min'] > (int) $filters['price_max']) {
                throw ValidationException::withMessages(['price_min' => __('Minimum price cannot be greater than maximum price.')]);
            }

            // Some installs may be mid-migration; avoid hard 500s when optional product columns
            // have not been applied yet.
            $hasCategoryCol = false;
            $hasOverviewCol = false;
            $hasDescriptionCol = false;
            try {
                $hasCategoryCol = Schema::hasColumn('vehicles', 'product_category_listing_option_id');
                $hasOverviewCol = Schema::hasColumn('vehicles', 'overview');
                $hasDescriptionCol = Schema::hasColumn('vehicles', 'description');
            } catch (\Throwable) {
                // Fail-open: treat as absent and fall back to basic fields.
            }

            $query = Vehicle::query()
                ->with(['images', 'categoryOption'])
                ->where('status', 'approved')
                ->latest();

            $search = isset($filters['q']) ? trim((string) $filters['q']) : '';
            if ($search !== '') {
                $like = '%'.$search.'%';
                $query->where(function ($builder) use ($like, $hasDescriptionCol, $hasOverviewCol) {
                    $builder->where('title', 'like', $like);
                    if ($hasDescriptionCol) {
                        $builder->orWhere('description', 'like', $like);
                    }
                    if ($hasOverviewCol) {
                        $builder->orWhere('overview', 'like', $like);
                    }
                });
            }

            $categoryId = (int) ($filters['product_category_listing_option_id'] ?? 0);
            if ($hasCategoryCol && $categoryId > 0) {
                $query->where('product_category_listing_option_id', $categoryId);
            }

            $sizeId = (int) ($filters['size_id'] ?? 0);
            if ($sizeId > 0) {
                $query->whereIn('id', VehicleVariant::query()
                    ->where('is_active', true)
                    ->where('size_listing_option_id', $sizeId)
                    ->select('vehicle_id')
                );
            }

            $colorId = (int) ($filters['color_id'] ?? 0);
            if ($colorId > 0) {
                $query->whereIn('id', VehicleVariant::query()
                    ->where('is_active', true)
                    ->where('color_listing_option_id', $colorId)
                    ->select('vehicle_id')
                );
            }

            $priceMin = (int) ($filters['price_min'] ?? 0);
            if ($priceMin > 0) {
                $query->where('price', '>=', $priceMin);
            }

            $priceMax = (int) ($filters['price_max'] ?? 0);
            if ($priceMax > 0) {
                $query->where('price', '<=', $priceMax);
            }

            $sort = (string) ($filters['sort'] ?? 'newest');
            match ($sort) {
                'price_low' => $query->orderBy('price'),
                'price_high' => $query->orderByDesc('price'),
                default => $query->latest(),
            };

            $vehicles = $query->paginate(9)->withQueryString();

            return view('pages.inventory.index', [
                'title' => ($page?->title ?: 'Shop'),
                'vehicles' => $vehicles,
                'filters' => array_merge($this->defaultInventoryFilters(), $filters),
                'filterOptions' => $filterOpts,
                'page' => $page,
                'sections' => $this->pageSections('inventory', [
                    'heading' => 'Shop the Collection',
                    'fallback_image' => 'asset/images/media/inventory-listing-fallback.jpg',
                ]),
            ]);
        } catch (\Throwable $e) {
            \Log::error('storefront.inventory failed', ['error' => $e->getMessage()]);

            return view('pages.inventory.index', [
                'title' => 'Shop',
                'vehicles' => Vehicle::query()->whereRaw('1=0')->paginate(9),
                'filters' => $this->defaultInventoryFilters(),
                'filterOptions' => ['categories' => collect()],
                'page' => null,
                'sections' => $this->pageSections('inventory', [
                    'heading' => 'Shop the Collection',
                    'fallback_image' => 'asset/images/media/inventory-listing-fallback.jpg',
                ]),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultInventoryFilters(): array
    {
        return [
            'q' => '',
            'product_category_listing_option_id' => '',
            'size_id' => '',
            'color_id' => '',
            'price_min' => '',
            'price_max' => '',
            'sort' => 'newest',
        ];
    }

    /**
     * Filter dropdown values: prefer admin listing catalog when present, else distinct from approved vehicles.
     *
     * @return array<string, mixed>
     */
    protected function approvedVehicleFilterOptions(): array
    {
        return VehicleListingCatalog::filterOptions();
    }

    public function vehicleShow(Request $request, string $slug = '')
    {
        // CMS row is optional: do not 404 listings when `listing-detail` is missing or inactive after a DB import.
        $page = CmsPage::query()->where('slug', 'listing-detail')->where('is_active', true)->first();
        $user = $request->user();

        $vehicle = Vehicle::query()
            ->with([
                'images',
                'user.vendorProfile',
                'categoryOption',
                'variants.sizeOption',
                'variants.colorOption',
            ])
            ->where('slug', $slug)
            ->when(! ($user && $user->hasRole('admin')), function ($query) use ($user) {
                $query->where(function ($visibility) use ($user) {
                    $visibility->where('status', 'approved');

                    if ($user) {
                        $visibility->orWhere('user_id', $user->id);
                    }
                });
            })
            ->firstOrFail();

        $siteMerged = SiteSettingDefaults::mergeWithDatabase(SiteSetting::allKeyed());

        $logoPath = trim((string) ($siteMerged['logo_path'] ?? ''));
        $logoUrl = trim((string) ($siteMerged['logo_url'] ?? ''));
        $sitePhone = trim((string) ($siteMerged['dealer_phone'] ?? '')) ?: trim((string) ($siteMerged['dealer_sales_phone'] ?? ''));
        $siteEmail = trim((string) ($siteMerged['dealer_public_email'] ?? '')) ?: (string) config('mail.from.address', '');
        $siteAddress = trim((string) ($siteMerged['dealer_address'] ?? ''));

        $posterName = trim((string) ($vehicle->user?->name ?? '')) ?: __('Store');
        $userAvatar = trim((string) ($vehicle->user?->avatar ?? ''));

        $sellerProfile = [
            'name' => $posterName,
            'phone' => $sitePhone,
            'photo_url' => $userAvatar !== '' ? $userAvatar : null,
            'fallback_logo_path' => $logoPath !== '' ? $logoPath : null,
            'fallback_logo_url' => $logoUrl !== '' ? $logoUrl : null,
        ];

        $siteContact = [
            'address' => $siteAddress,
            'phone' => $sitePhone,
            'email' => $siteEmail,
        ];

        // Similar products: same category if set, else most recent approvals.
        $similarVehicles = Vehicle::query()
            ->with(['images', 'categoryOption'])
            ->where('status', 'approved')
            ->whereKeyNot($vehicle->id)
            ->when(($catId = (int) ($vehicle->product_category_listing_option_id ?? 0)) > 0,
                fn ($q) => $q->where('product_category_listing_option_id', $catId)
            )
            ->orderByDesc('approved_at')
            ->orderByDesc('id')
            ->take(12)
            ->get();

        if ($similarVehicles->count() < 6) {
            $fallback = Vehicle::query()
                ->with('images')
                ->where('status', 'approved')
                ->whereKeyNot($vehicle->id)
                ->whereNotIn('id', $similarVehicles->pluck('id'))
                ->orderByDesc('approved_at')
                ->orderByDesc('id')
                ->take(6 - $similarVehicles->count())
                ->get();
            $similarVehicles = $similarVehicles->concat($fallback)->values();
        }

        $siteName = SiteBrand::displayName();
        $plainDesc = $vehicle->description
            ? Str::limit(strip_tags($vehicle->description), 160)
            : Str::limit(trim((string) ($vehicle->title ?? '')), 160);

        $cover = $vehicle->images->first();
        $listingUrl = request()->routeIs('product.show')
            ? route('product.show', ['slug' => $vehicle->slug], true)
            : route('inventory.show', ['slug' => $vehicle->slug], true);
        $ogImage = $cover ? url(VehicleImageUrl::url($cover->path)) : null;

        $isFavorited = $user && $user->favoriteVehicles()->whereKey($vehicle->id)->exists();

        return view('pages.inventory.show', [
            'title' => (($page?->title ?: $vehicle->title).' | '.$siteName),
            'metaDescription' => $page?->meta_description ?: $plainDesc,
            'canonicalUrl' => $listingUrl,
            'ogTitle' => $vehicle->title,
            'ogDescription' => $plainDesc,
            'ogUrl' => $listingUrl,
            'ogImage' => $ogImage,
            'slug' => $slug,
            'vehicle' => $vehicle,
            'sellerProfile' => $sellerProfile,
            'siteContact' => $siteContact,
            'similarVehicles' => $similarVehicles,
            'isFavorited' => $isFavorited,
            'productVariants' => $vehicle->variants,
            'page' => $page,
            'sections' => $this->pageSections('listing-detail', [
                'heading' => 'Product Detail',
            ]),
        ]);
    }

    public function compare()
    {
        $page = CmsPage::query()->where('slug', 'compare')->where('is_active', true)->first();
        $vehicles = Vehicle::query()
            ->with(['images', 'categoryOption'])
            ->whereIn('id', Compare::ids())
            ->get()
            ->sortBy(fn (Vehicle $v) => array_search($v->id, Compare::ids(), true))
            ->values();

        return view('pages.compare', [
            'title' => ($page?->title ?: 'Compare'),
            'vehicles' => $vehicles,
            'page' => $page,
            'sections' => $this->pageSections('compare', [
                'heading' => 'Compare Products',
                'intro' => 'Compare list is dynamic and comes from visitor selections.',
            ]),
        ]);
    }
}
