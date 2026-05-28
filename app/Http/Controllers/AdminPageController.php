<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\PageSection;
use App\Support\CmsNavigation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPageController extends Controller
{
    /**
     * @return array<string, array{label: string, default_title: string, default_description: string, fields: array<int, array{name: string, label: string, type: string, default: string, group?: string}>}>
     */
    protected function editablePages(): array
    {
        return [
            'home' => [
                'label' => 'Home',
                'default_title' => 'Home',
                'default_description' => 'Homepage SEO and section copy for the Vogue Atelier storefront.',
                'fields' => [
                    ['name' => 'hero_title', 'label' => 'Hero Title', 'type' => 'text', 'default' => 'Artisanship Redefined', 'group' => 'Hero'],
                    ['name' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea', 'default' => 'Luxury apparel and accessories cut, sewn, and finished in our Lagos atelier.', 'group' => 'Hero'],
                    ['name' => 'hero_cta_text', 'label' => 'Hero CTA Button Text', 'type' => 'text', 'default' => 'Explore Collection', 'group' => 'Hero'],
                    ['name' => 'hero_cta_href', 'label' => 'Hero CTA Link (path or URL)', 'type' => 'text', 'default' => '/shop', 'group' => 'Hero'],
                    ['name' => 'hero_image', 'label' => 'Hero Background Image', 'type' => 'image', 'default' => 'asset/images/media/home-hero-main.jpg', 'group' => 'Hero', 'preview' => 'thumbnail'],

                    ['name' => 'shop_categories_title', 'label' => 'Section title only', 'type' => 'text', 'default' => 'Shop Categories', 'group' => 'Shop categories (tiles come from Admin → Categories)'],
                    ['name' => 'bestsellers_title', 'label' => 'Section title only', 'type' => 'text', 'default' => 'The Bestsellers', 'group' => 'Bestsellers (up to 4 featured products)'],

                    ['name' => 'dealer_cta_bg', 'label' => 'Promo banner background', 'type' => 'image', 'default' => 'asset/images/media/home-cta-left.jpg', 'group' => 'Promo banner', 'preview' => 'thumbnail'],
                    ['name' => 'promo_eyebrow', 'label' => 'Promo eyebrow', 'type' => 'text', 'default' => 'LIMITED CAPSULE', 'group' => 'Promo banner'],
                    ['name' => 'promo_title', 'label' => 'Promo title', 'type' => 'text', 'default' => 'The Diaspora Series', 'group' => 'Promo banner'],
                    ['name' => 'promo_cta', 'label' => 'Promo button text', 'type' => 'text', 'default' => 'Explore Series', 'group' => 'Promo banner'],
                    ['name' => 'promo_cta_href', 'label' => 'Promo button link (path or URL)', 'type' => 'text', 'default' => '/shop', 'group' => 'Promo banner'],

                    ['name' => 'welcome_eyebrow', 'label' => 'Heritage eyebrow', 'type' => 'text', 'default' => 'OUR HERITAGE', 'group' => 'Heritage block'],
                    ['name' => 'welcome_title', 'label' => 'Heritage block title', 'type' => 'text', 'default' => 'Crafting a New Legacy', 'group' => 'Heritage block'],
                    ['name' => 'welcome_body', 'label' => 'Heritage block body', 'type' => 'textarea', 'default' => 'We collaborate with master artisans to bring exceptional pieces to a global audience. Each product reflects quality, story, and craft.', 'group' => 'Heritage block'],
                    ['name' => 'welcome_body_2', 'label' => 'Heritage block secondary paragraph (optional)', 'type' => 'textarea', 'default' => '', 'group' => 'Heritage block'],
                ],
            ],
            'inventory' => [
                'label' => 'Shop',
                'default_title' => 'Shop',
                'default_description' => 'Shop page heading and SEO. Product cards always load from active products.',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Shop Heading', 'type' => 'text', 'default' => 'Shop the Collection', 'group' => 'Page header'],
                    ['name' => 'fallback_image', 'label' => 'Product Card Fallback Image', 'type' => 'image', 'default' => 'asset/images/media/inventory-listing-fallback.jpg', 'group' => 'Media', 'preview' => 'thumbnail'],
                ],
            ],
            'contact' => [
                'label' => 'Contact',
                'default_title' => 'Contact Us',
                'default_description' => 'Contact page title and intro copy.',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Contact Heading', 'type' => 'text', 'default' => 'Contact Us', 'group' => 'Page intro'],
                    ['name' => 'intro', 'label' => 'Contact Intro', 'type' => 'textarea', 'default' => 'Reach our team using the form below.', 'group' => 'Page intro'],
                    ['name' => 'hero_image', 'label' => 'Hero / Header Image', 'type' => 'image', 'default' => 'asset/images/media/contact-hero-bg.jpg', 'group' => 'Media', 'preview' => 'thumbnail'],
                    ['name' => 'map_address', 'label' => 'Map Location Address (optional, overrides default address)', 'type' => 'text', 'default' => '', 'group' => 'Media'],
                    ['name' => 'map_embed_url', 'label' => 'Google Maps Embed URL (optional)', 'type' => 'text', 'default' => '', 'group' => 'Media'],
                    ['name' => 'map_fallback_image', 'label' => 'Map Fallback Image', 'type' => 'image', 'default' => 'asset/images/media/contact-map.jpg', 'group' => 'Media', 'preview' => 'thumbnail'],

                    ['name' => 'sales_address', 'label' => 'Sales Address', 'type' => 'textarea', 'default' => '1840 E Garvey Ave South West Covina, CA 91791', 'group' => 'Sales Department'],
                    ['name' => 'sales_phone', 'label' => 'Sales Phone', 'type' => 'text', 'default' => '(888) 354-1781', 'group' => 'Sales Department'],
                    ['name' => 'sales_hours', 'label' => 'Sales Hours', 'type' => 'textarea', 'default' => "Mon - Fri: 09:00AM - 09:00PM\nSaturday: 09:00AM - 07:00PM\nSunday: Closed", 'group' => 'Sales Department'],

                    ['name' => 'parts_address', 'label' => 'Parts Address', 'type' => 'textarea', 'default' => '1840 E Garvey Ave South West Covina, CA 91791', 'group' => 'Parts Department'],
                    ['name' => 'parts_phone', 'label' => 'Parts Phone', 'type' => 'text', 'default' => '(888) 354-1782', 'group' => 'Parts Department'],
                    ['name' => 'parts_hours', 'label' => 'Parts Hours', 'type' => 'textarea', 'default' => "Mon - Fri: 09:00AM - 09:00PM\nSaturday: 09:00AM - 07:00PM\nSunday: Closed", 'group' => 'Parts Department'],

                    ['name' => 'renting_address', 'label' => 'Renting Address', 'type' => 'textarea', 'default' => '1840 E Garvey Ave South West Covina, CA 91791', 'group' => 'Renting Department'],
                    ['name' => 'renting_phone', 'label' => 'Renting Phone', 'type' => 'text', 'default' => '(888) 354-1783', 'group' => 'Renting Department'],
                    ['name' => 'renting_hours', 'label' => 'Renting Hours', 'type' => 'textarea', 'default' => "Mon - Fri: 09:00AM - 09:00PM\nSaturday: 09:00AM - 07:00PM\nSunday: Closed", 'group' => 'Renting Department'],
                ],
            ],
            'about' => [
                'label' => 'About',
                'default_title' => 'About Us',
                'default_description' => 'About page SEO and section content',
                'fields' => [
                    ['name' => 'hero_image', 'label' => 'Hero Background Image', 'type' => 'image', 'default' => 'asset/images/media/about-hero-bg.jpg', 'group' => 'Hero', 'preview' => 'thumbnail'],
                    ['name' => 'established_year', 'label' => 'Established Year Text', 'type' => 'text', 'default' => '2018', 'group' => 'Hero'],
                    ['name' => 'hero_stat_value', 'label' => 'Hero Stat Value (e.g. 25+)', 'type' => 'text', 'default' => '50+', 'group' => 'Hero'],
                    ['name' => 'hero_stat_label', 'label' => 'Hero Stat Label (e.g. Years of Excellence)', 'type' => 'text', 'default' => 'Pieces in the collection', 'group' => 'Hero'],
                    ['name' => 'heading', 'label' => 'Hero Heading', 'type' => 'text', 'default' => 'WELCOME TO VOGUE ATELIER', 'group' => 'Hero'],
                    ['name' => 'intro', 'label' => 'Hero Paragraph', 'type' => 'textarea', 'default' => 'A Lagos-based atelier shaping luxury silhouettes for the modern global wardrobe. Heritage techniques, contemporary cuts, and material integrity guide every piece we release.', 'group' => 'Hero'],
                    ['name' => 'hero_primary_cta_text', 'label' => 'Hero Button Text', 'type' => 'text', 'default' => 'Read our story', 'group' => 'Hero'],
                    ['name' => 'hero_primary_cta_href', 'label' => 'Hero Button Link', 'type' => 'text', 'default' => '/about', 'group' => 'Hero'],

                    ['name' => 'values_title', 'label' => 'Core Values Heading', 'type' => 'text', 'default' => 'CORE VALUES', 'group' => 'Core values'],
                    ['name' => 'val_1_title', 'label' => 'Value 1 Title', 'type' => 'text', 'default' => 'Material Integrity', 'group' => 'Core values'],
                    ['name' => 'val_1_body', 'label' => 'Value 1 Body', 'type' => 'textarea', 'default' => 'Heavy-weight silks, hand-burnished leathers, and ethically sourced wools — each garment is cut from textiles we trust.', 'group' => 'Core values'],
                    ['name' => 'val_2_title', 'label' => 'Value 2 Title', 'type' => 'text', 'default' => 'Artisan Craft', 'group' => 'Core values'],
                    ['name' => 'val_2_body', 'label' => 'Value 2 Body', 'type' => 'textarea', 'default' => 'Every silhouette is hand-finished in our Lagos atelier, combining heritage technique with sculpted, contemporary tailoring.', 'group' => 'Core values'],
                    ['name' => 'val_3_title', 'label' => 'Value 3 Title', 'type' => 'text', 'default' => 'Considered Service', 'group' => 'Core values'],
                    ['name' => 'val_3_body', 'label' => 'Value 3 Body', 'type' => 'textarea', 'default' => 'Personal styling, made-to-measure tailoring, and a careful concierge experience for every order.', 'group' => 'Core values'],
                    ['name' => 'values_grid_1', 'label' => 'Values Grid Image 1', 'type' => 'image', 'default' => 'asset/images/media/about-values-1.jpg', 'group' => 'Core values', 'preview' => 'thumbnail'],
                    ['name' => 'values_grid_2', 'label' => 'Values Grid Image 2', 'type' => 'image', 'default' => 'asset/images/media/about-values-2.jpg', 'group' => 'Core values', 'preview' => 'thumbnail'],
                    ['name' => 'values_grid_3', 'label' => 'Values Grid Image 3', 'type' => 'image', 'default' => 'asset/images/media/about-values-3.jpg', 'group' => 'Core values', 'preview' => 'thumbnail'],
                    ['name' => 'values_grid_4', 'label' => 'Values Grid Image 4', 'type' => 'image', 'default' => 'asset/images/media/about-values-4.jpg', 'group' => 'Core values', 'preview' => 'thumbnail'],

                    ['name' => 'gallery_title', 'label' => 'Gallery title', 'type' => 'text', 'default' => 'Media Gallery', 'group' => 'Gallery'],
                    ['name' => 'gallery', 'label' => 'Media Gallery', 'type' => 'gallery', 'default' => '[]', 'group' => 'Gallery'],

                    ['name' => 'advantages_title', 'label' => 'Quick Links title', 'type' => 'text', 'default' => 'Quick Links', 'group' => 'Quick Links'],
                    ['name' => 'adv_1_icon', 'label' => 'Card 1 Icon', 'type' => 'text', 'default' => 'shopping_bag', 'group' => 'Quick Links'],
                    ['name' => 'adv_1_title', 'label' => 'Card 1 Title', 'type' => 'text', 'default' => 'Shop the new arrivals', 'group' => 'Quick Links'],
                    ['name' => 'adv_1_body', 'label' => 'Card 1 Body', 'type' => 'textarea', 'default' => 'Discover the season’s freshest silhouettes — refreshed every drop.', 'group' => 'Quick Links'],
                    ['name' => 'adv_1_href', 'label' => 'Card 1 Link', 'type' => 'text', 'default' => '/shop', 'group' => 'Quick Links'],
                    ['name' => 'adv_2_icon', 'label' => 'Card 2 Icon', 'type' => 'text', 'default' => 'favorite', 'group' => 'Quick Links'],
                    ['name' => 'adv_2_title', 'label' => 'Card 2 Title', 'type' => 'text', 'default' => 'Save your favourites', 'group' => 'Quick Links'],
                    ['name' => 'adv_2_body', 'label' => 'Card 2 Body', 'type' => 'textarea', 'default' => 'Create an account to build wishlists, track orders, and unlock early-access drops.', 'group' => 'Quick Links'],
                    ['name' => 'adv_2_href', 'label' => 'Card 2 Link', 'type' => 'text', 'default' => '/register', 'group' => 'Quick Links'],
                    ['name' => 'adv_3_icon', 'label' => 'Card 3 Icon', 'type' => 'text', 'default' => 'support_agent', 'group' => 'Quick Links'],
                    ['name' => 'adv_3_title', 'label' => 'Card 3 Title', 'type' => 'text', 'default' => 'Personal styling', 'group' => 'Quick Links'],
                    ['name' => 'adv_3_body', 'label' => 'Card 3 Body', 'type' => 'textarea', 'default' => 'Book a one-to-one styling session with our atelier for made-to-measure pieces.', 'group' => 'Quick Links'],
                    ['name' => 'adv_3_href', 'label' => 'Card 3 Link', 'type' => 'text', 'default' => '/contact', 'group' => 'Quick Links'],

                    ['name' => 'testimonials_title', 'label' => 'Testimonials title', 'type' => 'text', 'default' => 'Customer Testimonials', 'group' => 'Testimonials'],
                    ['name' => 'testimonial_1_body', 'label' => 'Featured Testimonial Quote', 'type' => 'textarea', 'default' => 'The cut, the fabric, and the fit are flawless. Vogue Atelier has redefined how I dress for every occasion — sculpted, modern, and effortlessly luxurious.', 'group' => 'Testimonials'],
                    ['name' => 'testimonial_1_author', 'label' => 'Testimonial Author', 'type' => 'text', 'default' => 'Adaeze N.', 'group' => 'Testimonials'],
                    ['name' => 'testimonial_1_brand', 'label' => 'Testimonial Author Role/Location', 'type' => 'text', 'default' => 'Editor-in-chief, Lagos', 'group' => 'Testimonials'],
                ],
            ],
            'faq' => [
                'label' => 'FAQ',
                'default_title' => 'Frequently Asked Questions',
                'default_description' => 'FAQ page copy and SEO metadata.',
                'fields' => [
                    ['name' => 'kicker', 'label' => 'Header Kicker', 'type' => 'text', 'default' => 'Need Help?', 'group' => 'Page hero'],
                    ['name' => 'heading', 'label' => 'Header Title', 'type' => 'text', 'default' => 'HELP CENTER', 'group' => 'Page hero'],
                    ['name' => 'intro', 'label' => 'Header Intro', 'type' => 'textarea', 'default' => 'Everything you need to know about the Vogue Atelier experience — sizing, shipping, styling, and aftercare.', 'group' => 'Page hero'],
                    ['name' => 'hero_image', 'label' => 'Hero Background Image', 'type' => 'image', 'default' => 'asset/images/media/faq-hero-bg.jpg', 'group' => 'Media', 'preview' => 'thumbnail'],

                    ['name' => 'cat_1_title', 'label' => 'Category 1 Title', 'type' => 'text', 'default' => 'Orders & Shipping', 'group' => 'Category 1: Orders'],
                    ['name' => 'cat_1_icon', 'label' => 'Category 1 Icon', 'type' => 'text', 'default' => 'shopping_bag', 'group' => 'Category 1: Orders'],
                    ['name' => 'cat_1_faqs', 'label' => 'Category 1 FAQs', 'type' => 'repeater', 'default' => '[]', 'group' => 'Category 1: Orders', 'schema' => [
                        ['name' => 'q', 'label' => 'Question', 'type' => 'text'],
                        ['name' => 'a', 'label' => 'Answer', 'type' => 'textarea'],
                    ]],

                    ['name' => 'cat_2_title', 'label' => 'Category 2 Title', 'type' => 'text', 'default' => 'Sizing & Fit', 'group' => 'Category 2: Sizing'],
                    ['name' => 'cat_2_icon', 'label' => 'Category 2 Icon', 'type' => 'text', 'default' => 'straighten', 'group' => 'Category 2: Sizing'],
                    ['name' => 'cat_2_faqs', 'label' => 'Category 2 FAQs', 'type' => 'repeater', 'default' => '[]', 'group' => 'Category 2: Sizing', 'schema' => [
                        ['name' => 'q', 'label' => 'Question', 'type' => 'text'],
                        ['name' => 'a', 'label' => 'Answer', 'type' => 'textarea'],
                    ]],

                    ['name' => 'cat_3_title', 'label' => 'Category 3 Title', 'type' => 'text', 'default' => 'Returns & Aftercare', 'group' => 'Category 3: Returns'],
                    ['name' => 'cat_3_icon', 'label' => 'Category 3 Icon', 'type' => 'text', 'default' => 'autorenew', 'group' => 'Category 3: Returns'],
                    ['name' => 'cat_3_faqs', 'label' => 'Category 3 FAQs', 'type' => 'repeater', 'default' => '[]', 'group' => 'Category 3: Returns', 'schema' => [
                        ['name' => 'q', 'label' => 'Question', 'type' => 'text'],
                        ['name' => 'a', 'label' => 'Answer', 'type' => 'textarea'],
                    ]],

                    ['name' => 'cat_4_title', 'label' => 'Category 4 Title', 'type' => 'text', 'default' => 'Personal Styling', 'group' => 'Category 4: Styling'],
                    ['name' => 'cat_4_icon', 'label' => 'Category 4 Icon', 'type' => 'text', 'default' => 'auto_awesome', 'group' => 'Category 4: Styling'],
                    ['name' => 'cat_4_faqs', 'label' => 'Category 4 FAQs', 'type' => 'repeater', 'default' => '[]', 'group' => 'Category 4: Styling', 'schema' => [
                        ['name' => 'q', 'label' => 'Question', 'type' => 'text'],
                        ['name' => 'a', 'label' => 'Answer', 'type' => 'textarea'],
                    ]],

                    ['name' => 'cta_title', 'label' => 'CTA Title', 'type' => 'text', 'default' => 'STILL HAVE QUESTIONS?', 'group' => 'CTA Section'],
                    ['name' => 'cta_body', 'label' => 'CTA Body', 'type' => 'textarea', 'default' => 'Our concierge team is available seven days a week to help with sizing, styling, or order enquiries.', 'group' => 'CTA Section'],
                    ['name' => 'cta_image', 'label' => 'CTA Side Image', 'type' => 'image', 'default' => 'asset/images/media/faq-cta.jpg', 'group' => 'CTA Section', 'preview' => 'thumbnail'],
                ],
            ],
            'compare' => [
                'label' => 'Compare',
                'default_title' => 'Compare Products',
                'default_description' => 'Compare page heading and intro copy.',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Compare Heading', 'type' => 'text', 'default' => 'Compare Products', 'group' => 'Page intro'],
                    ['name' => 'intro', 'label' => 'Compare Intro', 'type' => 'textarea', 'default' => 'Compare list is dynamic and comes from visitor selections.', 'group' => 'Page intro'],
                ],
            ],
            'privacy-policy' => [
                'label' => 'Privacy Policy',
                'default_title' => 'Privacy Policy',
                'default_description' => 'Privacy policy content for store customers.',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Page heading', 'type' => 'text', 'default' => 'Privacy Policy', 'group' => 'Content'],
                    ['name' => 'body', 'label' => 'Policy body', 'type' => 'textarea', 'default' => "This Privacy Policy explains how we collect, use, and protect information when you use our online store.\n\nIf you create an account, sign in (including via Google), save favourites, place orders, or contact us, we may process the information you provide to deliver these features.\n\nWe do not sell your personal information. We use it to operate the store, process and ship your orders, communicate with you, prevent fraud, and comply with legal obligations.\n\nFor questions about this policy or to request access, correction, or deletion of your data, please use the Contact page.", 'group' => 'Content'],
                ],
            ],
            'terms' => [
                'label' => 'Terms & Conditions',
                'default_title' => 'Terms & Conditions',
                'default_description' => 'Terms and conditions for shopping with us.',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Page heading', 'type' => 'text', 'default' => 'Terms & Conditions', 'group' => 'Content'],
                    ['name' => 'body', 'label' => 'Terms body', 'type' => 'textarea', 'default' => "These Terms & Conditions govern your use of our online store.\n\nBy using the site, you agree not to misuse the platform, attempt unauthorized access, or submit false or misleading information when placing an order.\n\nProduct details, pricing, and stock availability are provided in good faith and may change. We reserve the right to refuse or cancel any order at our discretion.\n\nIf you place an order, you agree that we may contact you using the information you provide for fulfilment, shipping, and aftercare.\n\nWe may suspend or close accounts that violate these terms. These terms may be updated from time to time; continued use indicates acceptance of updates.", 'group' => 'Content'],
                ],
            ],
            'listing-detail' => [
                'label' => 'Product Detail',
                'default_title' => 'Product Detail',
                'default_description' => 'Product detail page heading and intro copy.',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Product Detail Heading', 'type' => 'text', 'default' => 'Product Detail', 'group' => 'Page intro'],
                    ['name' => 'intro', 'label' => 'Product Detail Intro', 'type' => 'textarea', 'default' => 'Product details and gallery are dynamic from the product record.', 'group' => 'Page intro'],
                ],
            ],
        ];
    }

    /**
     * @param  array<int, array{name: string, label: string, type: string, default: string, group?: string}>  $fields
     * @return array<string, string>
     */
    protected function sectionValues(string $slug, array $fields): array
    {
        $stored = PageSection::query()
            ->where('page', $slug)
            ->pluck('content', 'section_key');
        $out = [];

        foreach ($fields as $field) {
            $out[$field['name']] = (string) ($stored[$field['name']] ?? $field['default']);
        }

        return $out;
    }

    public function index(): View
    {
        $editable = $this->editablePages();
        $existing = CmsPage::query()
            ->whereIn('slug', array_keys($editable))
            ->get(['slug', 'updated_at', 'is_active'])
            ->keyBy('slug');

        return view('admin.pages.index', [
            'pages' => $editable,
            'existing' => $existing,
        ]);
    }

    public function edit(string $slug): View
    {
        $editable = $this->editablePages();
        abort_unless(isset($editable[$slug]), 404);

        $defaults = $editable[$slug];
        $page = CmsPage::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'title' => $defaults['default_title'],
                'meta_description' => $defaults['default_description'],
                'content_html' => '',
                'is_active' => true,
            ]
        );

        return view('admin.pages.edit', [
            'slug' => $slug,
            'pageInfo' => $defaults,
            'page' => $page,
            'sectionValues' => $this->sectionValues($slug, $defaults['fields']),
        ]);
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $editable = $this->editablePages();
        abort_unless(isset($editable[$slug]), 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'content_html' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // About + listing-detail are section-fields only: never persist arbitrary HTML dumps.
        if (in_array($slug, ['about', 'listing-detail'], true)) {
            $data['content_html'] = '';
        }

        CmsPage::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'title' => $data['title'],
                'meta_description' => $data['meta_description'] ?? null,
                'content_html' => $data['content_html'] ?? '',
                'is_active' => (bool) ($data['is_active'] ?? false),
            ]
        );

        CmsNavigation::flushCache();

        foreach ($editable[$slug]['fields'] as $field) {
            $name = $field['name'];
            $value = $request->input('sections.'.$name, $field['default']);
            PageSection::query()->updateOrCreate(
                [
                    'page' => $slug,
                    'section_key' => $name,
                ],
                [
                    'content_type' => $field['type'],
                    'content' => is_string($value) ? $value : (string) $value,
                ]
            );
        }

        return redirect()
            ->route('admin.pages.edit', ['slug' => $slug])
            ->with('status', 'Page updated successfully.');
    }
}
