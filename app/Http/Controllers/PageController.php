<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Models\Vehicle;
use App\Support\Compare;
use App\Support\SiteSettingDefaults;
use App\Support\VehicleImageUrl;
use App\Support\VehicleListingCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        $siteName = config('app.name');
        $recentVehicles = Vehicle::query()
            ->with(['images', 'transmissionOption', 'makeOption', 'modelOption'])
            ->where('status', 'approved')
            // Homepage "recent cars" should reflect newly approved/added inventory,
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
            'hero_title' => 'Lorem ipsum dolor sit amet',
            'hero_subtitle' => 'Consectetur adipiscing elit, sed do eiusmod tempor incididunt',
            'hero_description' => '',
            'hero_cta_text' => 'Lorem CTA',
            'hero_cta_href' => '/inventory',
            'home_search_label' => 'Lorem ipsum — search inventory',
            'recent_title' => 'Lorem dolor sit amet',
            'recent_subtitle' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Cards below are live listings.',
            'hero_image' => 'asset/images/media/home-hero-main.jpg',
            'dealer_cta_bg' => 'asset/images/media/home-cta-left.jpg',
            'dealer_cta_left_icon' => 'directions_car',
            'dealer_cta_right_icon' => 'sell',
            'cta_left_title' => 'Looking for a car?',
            'cta_left_body' => 'Our cars are delivered fully-registered with all requirements completed. We\'ll deliver your car wherever you are.',
            'cta_left_button_text' => 'Inventory',
            'cta_left_button_href' => '/inventory',
            'cta_right_title' => 'Want to sell a car?',
            'cta_right_body' => 'Receive the absolute best value for your trade-in vehicle. We even handle all paperwork. Schedule appointment!',
            'cta_right_button_text' => 'Sell your car',
            'cta_right_button_href' => '/register',
            'feat1_title' => 'Lorem ipsum',
            'feat1_body' => 'Dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero.',
            'feat2_title' => 'Dolor sit amet',
            'feat2_body' => 'Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet.',
            'feat3_title' => 'Consectetur elit',
            'feat3_body' => 'Fusce nec tellus sed augue semper porta. Mauris massa. Vestibulum lacinia arcu eget nulla.',
            'welcome_title' => 'Lorem ipsum welcome block',
            'welcome_body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper porta.',
            'welcome_video_url' => '',
            // Statistics + testimonial section keys removed (plan requirement).
            'prefooter_title' => 'Lorem ipsum — questions?',
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
        $siteName = config('app.name');
        $sections = $this->pageSections('about', [
            'hero_image' => 'asset/images/media/about-hero-bg.jpg',
            'established_year' => '1999',
            'hero_stat_value' => '25+',
            'hero_stat_label' => 'Years of Excellence',
            'heading' => 'WELCOME TO THE MOTORS',
            'intro' => 'Experience the pinnacle of automotive engineering and white-glove service. We curate the world\'s most exceptional vehicles for the discerning driver who demands nothing less than absolute mechanical perfection.',
            'hero_primary_cta_text' => 'Learn Our History',
            'hero_primary_cta_href' => '/about',

            'values_title' => 'CORE VALUES',
            'val_1_title' => 'Integrity First',
            'val_1_body' => 'Transparent pricing and rigorous history checks for every vehicle in our showroom.',
            'val_2_title' => 'Mechanical Excellence',
            'val_2_body' => 'Our master technicians conduct a 200-point inspection ensuring performance meets factory standards.',
            'val_3_title' => 'Client Concierge',
            'val_3_body' => 'Dedicated advisors providing personalized acquisition strategies and lifelong maintenance support.',
            'values_grid_1' => 'asset/images/media/about-values-1.jpg',
            'values_grid_2' => 'asset/images/media/about-values-2.jpg',
            'values_grid_3' => 'asset/images/media/about-values-3.jpg',
            'values_grid_4' => 'asset/images/media/about-values-4.jpg',

            'gallery_title' => 'Media Gallery',
            'gallery' => '[]',

            'advantages_title' => 'Quick Links',
            'adv_1_icon' => 'sell',
            'adv_1_title' => 'Do you want to sell a car?',
            'adv_1_body' => 'Get a competitive appraisal and same-day payment from our acquisition team.',
            'adv_1_href' => '/sell',
            'adv_2_icon' => 'directions_car',
            'adv_2_title' => 'Looking for a new car?',
            'adv_2_body' => 'Browse our curated collection of premium inventory and certified pre-owned units.',
            'adv_2_href' => '/inventory',
            'adv_3_icon' => 'build',
            'adv_3_title' => 'Schedule a service?',
            'adv_3_body' => 'Book an appointment with our specialist mechanics for maintenance or tuning.',
            'adv_3_href' => '/service',

            'testimonials_title' => 'Customer Testimonials',
            'testimonial_1_body' => 'The acquisition process for my vintage collection was handled with unparalleled professionalism. Velocity Motors doesn\'t just sell cars; they curate legacies.',
            'testimonial_1_author' => 'John Doe',
            'testimonial_1_brand' => 'Private Collector, London',
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
        $siteName = config('app.name');

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
                'sales_address' => SiteSetting::getValue('dealer_address', '1840 E Garvey Ave South West Covina, CA 91791'),
                'sales_phone' => SiteSetting::getValue('dealer_sales_phone', '(888) 354-1781'),
                'sales_hours' => SiteSetting::getValue('dealer_sales_hours', "Mon - Fri: 09:00AM - 09:00PM\nSaturday: 09:00AM - 07:00PM\nSunday: Closed"),
                'parts_address' => SiteSetting::getValue('dealer_address', '1840 E Garvey Ave South West Covina, CA 91791'),
                'parts_phone' => '(888) 354-1782',
                'parts_hours' => "Mon - Fri: 09:00AM - 09:00PM\nSaturday: 09:00AM - 07:00PM\nSunday: Closed",
                'renting_address' => SiteSetting::getValue('dealer_address', '1840 E Garvey Ave South West Covina, CA 91791'),
                'renting_phone' => '(888) 354-1783',
                'renting_hours' => "Mon - Fri: 09:00AM - 09:00PM\nSaturday: 09:00AM - 07:00PM\nSunday: Closed",
            ]),
        ]);
    }

    public function faq()
    {
        $page = CmsPage::query()->where('slug', 'faq')->where('is_active', true)->firstOrFail();
        $siteName = config('app.name');
        $sections = $this->pageSections('faq', [
            'kicker' => 'Need Help?',
            'heading' => 'HELP CENTER',
            'intro' => 'Everything you need to know about the Apex Automotive experience, from acquisition to elite performance servicing.',
            'hero_image' => 'asset/images/media/faq-hero-bg.jpg',
            'cat_1_title' => 'Buying & Inventory',
            'cat_1_icon' => 'directions_car',
            'cat_1_faqs' => json_encode([
                ['q' => 'Can I reserve a vehicle before visiting the dealership?', 'a' => 'Yes. We offer a digital reservation service where you can place a fully refundable deposit on any vehicle for up to 48 hours.'],
                ['q' => 'What kind of inspection do vehicles undergo?', 'a' => 'Every vehicle in our inventory passes a rigorous 172-point Certification process by factory-trained technicians.'],
                ['q' => 'Do you offer nationwide shipping?', 'a' => 'Absolutely. We utilize specialized enclosed carriers to ship vehicles anywhere in the continental United States.'],
            ]),
            'cat_2_title' => 'Financing & Trade',
            'cat_2_icon' => 'payments',
            'cat_2_faqs' => json_encode([
                ['q' => 'How is my trade-in value determined?', 'a' => 'We use real-time market data alongside a physical appraisal to provide the most competitive value.'],
                ['q' => 'Do you work with luxury-specific lenders?', 'a' => 'Yes, our finance department partners with premier financial institutions that understand high-value vehicle assets.'],
            ]),
            'cat_3_title' => 'Performance Service',
            'cat_3_icon' => 'build_circle',
            'cat_3_faqs' => json_encode([
                ['q' => 'What performance tuning services do you offer?', 'a' => 'From stage 1 ECU remapping to full exhaust systems and suspension setups, our specialists handle it all.'],
            ]),
            'cat_4_title' => 'Selling to Apex',
            'cat_4_icon' => 'sell',
            'cat_4_faqs' => json_encode([
                ['q' => 'What documents are needed to sell my car?', 'a' => 'You\'ll need the title, valid ID, and maintenance records. Our team handles all transfer paperwork for you.'],
            ]),
            'cta_title' => 'STILL SEEKING ANSWERS?',
            'cta_body' => 'Our automotive concierges are available 7 days a week to assist with technical specifications or test drives.',
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
        $siteName = config('app.name');
        $sections = $this->pageSections('privacy-policy', [
            'heading' => 'Privacy Policy',
            'body' => "This Privacy Policy explains how we collect, use, and protect information when you use our marketplace website.\n\nIf you create an account, sign in (including via Google), save listings, submit inquiries, or contact us, we may process the information you provide to deliver these features.\n\nWe do not sell your personal information. We use your information to operate the site, communicate with you, prevent fraud, and comply with legal obligations.\n\nFor questions about this policy or to request access, correction, or deletion of your data, please use the Contact page.",
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
        $siteName = config('app.name');
        $sections = $this->pageSections('terms', [
            'heading' => 'Terms & Conditions',
            'body' => "These Terms & Conditions govern your use of our marketplace website.\n\nBy using the site, you agree not to misuse the platform, attempt unauthorized access, or submit false or misleading information.\n\nListings may be posted by dealers and by staff accounts. Listing details, pricing, and availability can change and are provided for informational purposes.\n\nIf you submit an inquiry, you agree that we may contact you using the information you provide.\n\nWe may suspend or terminate accounts that violate these terms. These terms may be updated from time to time; continued use indicates acceptance of updates.",
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

    public function makesIndex()
    {
        $makes = VehicleListingCatalog::activeMakeNavTiles();
        $title = __('Shop by make');

        return view('pages.makes', [
            'title' => $title,
            'metaDescription' => __('Browse all vehicle makes and open matching inventory.'),
            'canonicalUrl' => route('makes.index', [], true),
            'ogTitle' => $title,
            'ogDescription' => __('Browse all vehicle makes and open matching inventory.'),
            'ogUrl' => route('makes.index', [], true),
            'ogImage' => null,
            'makes' => $makes,
        ]);
    }

    public function inventory(Request $request)
    {
        $page = CmsPage::query()->where('slug', 'inventory')->where('is_active', true)->first();
        $yearUpper = (int) date('Y') + 1;
        $filterOpts = $this->approvedVehicleFilterOptions();

        $idIn = function (Collection $rows): array {
            $ids = $rows->pluck('id')->filter()->values()->all();

            return $ids === [] ? [] : [Rule::in($ids)];
        };

        $extColors = collect($filterOpts['exterior_colors'] ?? []);
        $extRule = ['nullable', 'string', 'max:255'];
        if ($extColors instanceof Collection && $extColors->isNotEmpty()) {
            $extRule[] = Rule::in(array_values(array_unique(array_merge([''], $extColors->all()))));
        }

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'street_q' => ['nullable', 'string', 'max:1000'],
            'condition_listing_option_id' => array_merge(['nullable', 'integer'], $idIn(collect($filterOpts['conditions'] ?? []))),
            'country_listing_option_id' => array_merge(['nullable', 'integer'], $idIn(collect($filterOpts['countries'] ?? []))),
            'type_listing_option_id' => array_merge(['nullable', 'integer'], $idIn(collect($filterOpts['vehicle_origin_types'] ?? []))),
            'make_listing_option_id' => array_merge(['nullable', 'integer'], $idIn(collect($filterOpts['makes'] ?? []))),
            'model_listing_option_id' => array_merge(['nullable', 'integer'], $idIn(collect($filterOpts['model_matrix'] ?? collect())->pluck('model_id'))),
            'fuel_type_listing_option_id' => array_merge(['nullable', 'integer'], $idIn(collect($filterOpts['fuel_types'] ?? []))),
            'transmission_listing_option_id' => array_merge(['nullable', 'integer'], $idIn(collect($filterOpts['transmissions'] ?? []))),
            'body_type_listing_option_id' => array_merge(['nullable', 'integer'], $idIn(collect($filterOpts['body_types'] ?? []))),
            'drive_listing_option_id' => array_merge(['nullable', 'integer'], $idIn(collect($filterOpts['drives'] ?? []))),
            'exterior_color' => $extRule,
            'vin' => ['nullable', 'string', 'max:255'],
            'year_min' => ['nullable', 'integer', 'min:1900', 'max:'.$yearUpper],
            'year_max' => ['nullable', 'integer', 'min:1900', 'max:'.$yearUpper],
            'mileage_min' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'mileage_max' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'price_min' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'price_max' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'sort' => ['nullable', 'string', Rule::in(['newest', 'price_low', 'price_high', 'year_new', 'year_old'])],
        ]);

        $mk = (int) ($filters['make_listing_option_id'] ?? 0);
        $md = (int) ($filters['model_listing_option_id'] ?? 0);
        if ($mk > 0 && $md > 0 && ($filterOpts['model_matrix'] ?? collect()) instanceof Collection && $filterOpts['model_matrix']->isNotEmpty()) {
            VehicleListingCatalog::assertMakeModelPairById($filterOpts['model_matrix'], $mk, $md);
        }
        if (! empty($filters['year_min']) && ! empty($filters['year_max']) && (int) $filters['year_min'] > (int) $filters['year_max']) {
            throw ValidationException::withMessages(['year_min' => __('Minimum year cannot be greater than maximum year.')]);
        }
        if (! empty($filters['mileage_min']) && ! empty($filters['mileage_max']) && (int) $filters['mileage_min'] > (int) $filters['mileage_max']) {
            throw ValidationException::withMessages(['mileage_min' => __('Minimum mileage cannot be greater than maximum mileage.')]);
        }
        if (! empty($filters['price_min']) && ! empty($filters['price_max']) && (int) $filters['price_min'] > (int) $filters['price_max']) {
            throw ValidationException::withMessages(['price_min' => __('Minimum price cannot be greater than maximum price.')]);
        }

        $query = Vehicle::query()
            ->with(['images', 'fuelTypeOption', 'transmissionOption', 'makeOption', 'modelOption'])
            ->where('status', 'approved')
            ->latest();

        $search = isset($filters['q']) ? trim((string) $filters['q']) : '';
        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(function ($builder) use ($like) {
                $builder
                    ->where('title', 'like', $like)
                    ->orWhere('street_address', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('makeOption', fn ($q) => $q->where('value', 'like', $like))
                    ->orWhereHas('modelOption', fn ($q) => $q->where('value', 'like', $like))
                    ->orWhereHas('countryOption', fn ($q) => $q->where('value', 'like', $like));
            });
        }

        $streetQ = isset($filters['street_q']) ? trim((string) $filters['street_q']) : '';
        if ($streetQ !== '') {
            $query->where('street_address', 'like', '%'.$streetQ.'%');
        }

        $cid = (int) ($filters['condition_listing_option_id'] ?? 0);
        if ($cid > 0) {
            $query->where('condition_listing_option_id', $cid);
        }

        $countryId = (int) ($filters['country_listing_option_id'] ?? 0);
        if ($countryId > 0) {
            $query->where('country_listing_option_id', $countryId);
        }

        $typeId = (int) ($filters['type_listing_option_id'] ?? 0);
        if ($typeId > 0) {
            $query->where('type_listing_option_id', $typeId);
        }

        $makeId = (int) ($filters['make_listing_option_id'] ?? 0);
        if ($makeId > 0) {
            $query->where('make_listing_option_id', $makeId);
        }

        $modelId = (int) ($filters['model_listing_option_id'] ?? 0);
        if ($modelId > 0) {
            $query->where('model_listing_option_id', $modelId);
        }

        foreach (
            [
                'fuel_type_listing_option_id',
                'transmission_listing_option_id',
                'body_type_listing_option_id',
                'drive_listing_option_id',
            ] as $field
        ) {
            $id = (int) ($filters[$field] ?? 0);
            if ($id > 0) {
                $query->where($field, $id);
            }
        }

        $ext = isset($filters['exterior_color']) ? trim((string) $filters['exterior_color']) : '';
        if ($ext !== '') {
            $query->where('exterior_color', $ext);
        }
        $vin = isset($filters['vin']) ? trim((string) $filters['vin']) : '';
        if ($vin !== '') {
            $query->where('vin', 'like', '%'.$vin.'%');
        }

        $yearMin = (int) ($filters['year_min'] ?? 0);
        if ($yearMin > 0) {
            $query->where('year', '>=', $yearMin);
        }

        $yearMax = (int) ($filters['year_max'] ?? 0);
        if ($yearMax > 0) {
            $query->where('year', '<=', $yearMax);
        }

        $priceMin = (int) ($filters['price_min'] ?? 0);
        if ($priceMin > 0) {
            $query->where('price', '>=', $priceMin);
        }

        $priceMax = (int) ($filters['price_max'] ?? 0);
        if ($priceMax > 0) {
            $query->where('price', '<=', $priceMax);
        }
        $mileageMin = (int) ($filters['mileage_min'] ?? 0);
        if ($mileageMin > 0) {
            $query->where('mileage', '>=', $mileageMin);
        }
        $mileageMax = (int) ($filters['mileage_max'] ?? 0);
        if ($mileageMax > 0) {
            $query->where('mileage', '<=', $mileageMax);
        }

        $sort = (string) ($filters['sort'] ?? 'newest');
        match ($sort) {
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'year_new' => $query->orderByDesc('year'),
            'year_old' => $query->orderBy('year'),
            default => $query->latest(),
        };

        $vehicles = $query->paginate(9)->withQueryString();

        return view('pages.inventory.index', [
            'title' => ($page?->title ?: 'Inventory'),
            'vehicles' => $vehicles,
            'filters' => array_merge($this->defaultInventoryFilters(), $filters),
            'filterOptions' => $filterOpts,
            'page' => $page,
            'sections' => $this->pageSections('inventory', [
                'heading' => 'Vehicles For Sale',
                'fallback_image' => 'asset/images/media/inventory-listing-fallback.jpg',
            ]),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultInventoryFilters(): array
    {
        return [
            'q' => '',
            'street_q' => '',
            'make_listing_option_id' => '',
            'model_listing_option_id' => '',
            'fuel_type_listing_option_id' => '',
            'transmission_listing_option_id' => '',
            'body_type_listing_option_id' => '',
            'drive_listing_option_id' => '',
            'exterior_color' => '',
            'vin' => '',
            'condition_listing_option_id' => '',
            'country_listing_option_id' => '',
            'type_listing_option_id' => '',
            'year_min' => '',
            'year_max' => '',
            'mileage_min' => '',
            'mileage_max' => '',
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

    public function vehicleShow(Request $request, string $slug = '2021-bmw-m4-competition')
    {
        // CMS row is optional: do not 404 listings when `listing-detail` is missing or inactive after a DB import.
        $page = CmsPage::query()->where('slug', 'listing-detail')->where('is_active', true)->first();
        $user = $request->user();

        $vehicle = Vehicle::query()
            ->with([
                'images',
                'user.vendorProfile',
                'makeOption',
                'modelOption',
                'bodyTypeOption',
                'transmissionOption',
                'fuelTypeOption',
                'driveOption',
                'countryOption',
                'typeOption',
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

        $posterName = trim((string) ($vehicle->user?->name ?? '')) ?: __('Dealer');
        $userAvatar = trim((string) ($vehicle->user?->avatar ?? ''));

        // Sidebar: always the account that posted the listing; phone = site default; avatar = user photo or site logo.
        $sellerProfile = [
            'name' => $posterName,
            'phone' => $sitePhone,
            'photo_url' => $userAvatar !== '' ? $userAvatar : null,
            'fallback_logo_path' => $logoPath !== '' ? $logoPath : null,
            'fallback_logo_url' => $logoUrl !== '' ? $logoUrl : null,
        ];

        $siteContact = [
            'address' => $siteAddress !== '' ? $siteAddress : trim((string) ($vehicle->street_address ?? '')),
            'phone' => $sitePhone,
            'email' => $siteEmail,
        ];

        $similarVehicles = Vehicle::query()
            ->with(['images', 'makeOption', 'modelOption'])
            ->where('status', 'approved')
            ->whereKeyNot($vehicle->id)
            ->where(function ($query) use ($vehicle) {
                $makeId = (int) ($vehicle->make_listing_option_id ?? 0);
                $modelId = (int) ($vehicle->model_listing_option_id ?? 0);
                if ($makeId > 0 && $modelId > 0) {
                    $query->where(function ($q) use ($makeId, $modelId) {
                        $q->where('make_listing_option_id', $makeId)->where('model_listing_option_id', $modelId);
                    })->orWhere('make_listing_option_id', $makeId);

                    return;
                }
                if ($makeId > 0) {
                    $query->where('make_listing_option_id', $makeId);

                    return;
                }
                $query->whereNotNull('id');
            })
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

        $siteName = config('app.name');
        $makeLabel = $vehicle->makeOption?->value ?? '';
        $modelLabel = $vehicle->modelOption?->value ?? '';
        $plainDesc = $vehicle->description
            ? Str::limit(strip_tags($vehicle->description), 160)
            : Str::limit(trim(($vehicle->title ?? '').' '.$makeLabel.' '.$modelLabel), 160);

        $cover = $vehicle->images->first();
        $listingUrl = route('inventory.show', ['slug' => $vehicle->slug], true);
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
            'page' => $page,
            'sections' => $this->pageSections('listing-detail', [
                'heading' => 'Vehicle Detail',
            ]),
        ]);
    }

    public function compare()
    {
        $page = CmsPage::query()->where('slug', 'compare')->where('is_active', true)->first();
        $vehicles = Vehicle::query()
            ->with([
                'images',
                'fuelTypeOption',
                'transmissionOption',
                'makeOption',
                'modelOption',
                'bodyTypeOption',
                'driveOption',
                'countryOption',
                'typeOption',
            ])
            ->whereIn('id', Compare::ids())
            ->get()
            ->sortBy(fn (Vehicle $v) => array_search($v->id, Compare::ids(), true))
            ->values();

        return view('pages.compare', [
            'title' => ($page?->title ?: 'Compare'),
            'vehicles' => $vehicles,
            'page' => $page,
            'sections' => $this->pageSections('compare', [
                'heading' => 'Compare Vehicles',
                'intro' => 'Compare list is dynamic and comes from visitor selections.',
            ]),
        ]);
    }
}
