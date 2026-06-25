<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\Coach;
use App\Models\GalleryItem;
use App\Models\LeadershipMember;
use App\Models\PageSection;
use App\Models\Program;
use App\Models\SiteSetting;
use App\Models\Tournament;
use App\Support\PlaceholderMedia;
use App\Support\SiteBrand;
use App\Support\SiteSettingDefaults;

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

    protected function resolvePublicPage(string $slug, string $defaultTitle, string $defaultDescription = ''): CmsPage
    {
        return CmsPage::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'title' => $defaultTitle,
                'meta_description' => $defaultDescription,
                'content_html' => '',
                'is_active' => true,
            ]
        );
    }

    public function home()
    {
        $page = CmsPage::query()->where('slug', 'home')->where('is_active', true)->firstOrFail();
        $siteName = SiteBrand::displayName();

        $homeSections = $this->pageSections('home', [
            'hero_eyebrow' => 'ELITE ACADEMY TRIALS OPEN',
            'hero_title' => "Developing Tomorrow's Football Stars Today",
            'hero_subtitle' => 'Structured football development for players aged 7–15 through elite coaching, competitive tournaments, and professional mentorship in Ibeju Lekki.',
            'hero_cta_text' => 'Register Now',
            'hero_cta_href' => '/register',
            'hero_image' => 'asset/images/powerblink/home-powerblink-fc-044.jpg',
            'about_preview_image' => 'asset/images/powerblink/home-powerblink-fc-045.jpg',
            'shop_categories_title' => 'Development Programs',
            'promo_eyebrow' => 'SEASON REGISTRATION',
            'promo_title' => 'Ready To Begin Your Football Journey?',
            'promo_cta' => 'Register Today',
            'welcome_eyebrow' => 'OUR MISSION',
            'welcome_title' => 'Elite Excellence in Ibeju Lekki',
            'welcome_body' => 'Powerblink Football Club Limited is a launchpad for dreams — a safe, world-class environment where young athletes transform raw passion into professional competence.',
        ]);

        $ogImage = PlaceholderMedia::url($homeSections['hero_image'] ?? '');

        return view('pages.powerblink.home', [
            'title' => $page->title.' | '.$siteName,
            'metaDescription' => $page->meta_description,
            'canonicalUrl' => route('home', [], true),
            'ogTitle' => $page->title,
            'ogDescription' => $page->meta_description,
            'ogUrl' => route('home', [], true),
            'ogImage' => $ogImage,
            'page' => $page,
            'sections' => $homeSections,
            'programs' => Program::query()
                ->with(['season', 'heroImage'])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->take(6)
                ->get(),
            'stats' => [
                'active_players' => \App\Models\Player::query()->where('status', 'active')->count(),
                'programs_count' => Program::query()->where('is_active', true)->count(),
                'coaches_count' => Coach::query()->where('is_active', true)->count(),
                'seasons_count' => \App\Models\Season::query()->where('is_active', true)->count(),
                'location' => SiteSetting::getValue('dealer_address', 'Lagos, Nigeria'),
            ],
            'featuredCoach' => Coach::query()
                ->with('photo')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->first(),
            'featuredTournament' => Tournament::query()
                ->with('featuredImage')
                ->whereIn('status', ['upcoming', 'active'])
                ->orderBy('start_date')
                ->first(),
        ]);
    }

    public function about()
    {
        $page = $this->resolvePublicPage('about', 'About Us', 'Our story, values, and coaching philosophy.');
        $siteName = SiteBrand::displayName();
        $sections = $this->pageSections('about', [
            'hero_image' => 'asset/images/powerblink/about-us-powerblink-fc-001.jpg',
            'hero_title' => 'Our Story',
            'philosophy_kicker' => 'Our Philosophy',
            'philosophy_title' => 'Excellence On and Off the Pitch',
            'philosophy_quote' => '"We develop players who compete with courage, discipline, and joy."',
            'story_paragraph_1' => 'PowerBlink FC is a youth football academy committed to holistic player development.',
            'story_paragraph_2' => 'Our programs blend technical training, match intelligence, and character building for athletes aged U7 through U15.',
            'story_cta_text' => 'View Programs',
            'story_cta_href' => '/register',
            'artisan_kicker' => 'Our Coaches',
            'artisan_title' => 'Licensed, Experienced Staff',
            'artisan_body' => 'Our coaching team brings UEFA and CAF qualifications alongside years of academy experience.',
            'artisan_image' => 'asset/images/powerblink/about-us-powerblink-fc-009.jpg',
            'artisan_location_label' => 'Location',
            'artisan_location_detail' => 'Lagos, Nigeria',
            'values_title' => 'Core Values',
            'val_1_title' => 'Discipline',
            'val_1_body' => 'Consistent effort and respect for teammates, coaches, and the game.',
            'val_2_title' => 'Development',
            'val_2_body' => 'Age-appropriate training that challenges players to grow every session.',
            'val_3_title' => 'Community',
            'val_3_body' => 'Families, players, and staff united around shared goals.',
            'newsletter_title' => 'Stay Connected',
            'newsletter_body' => 'Receive academy news and registration updates.',
        ]);

        return view('pages.powerblink.about', [
            'title' => $page->title.' | '.$siteName,
            'metaDescription' => $page->meta_description,
            'canonicalUrl' => route('about', [], true),
            'ogTitle' => $page->title,
            'ogDescription' => $page->meta_description,
            'ogUrl' => route('about', [], true),
            'ogImage' => PlaceholderMedia::url($sections['hero_image'] ?? ''),
            'page' => $page,
            'sections' => $sections,
            'leadership' => LeadershipMember::query()
                ->with('photo')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function contact()
    {
        $page = $this->resolvePublicPage('contact', 'Contact Us', 'Reach our academy team for registration and program inquiries.');
        $siteName = SiteBrand::displayName();
        $dealerEmail = SiteSetting::getValue('dealer_public_email', '') ?: SiteSettingDefaults::resolvedNotifyEmail();

        $contactSections = $this->pageSections('contact', [
            'hero_title' => 'Contact Powerblink FC',
            'hero_intro' => 'Our academy staff are available to answer questions about programs, trials, and registration.',
            'services_email' => $dealerEmail,
            'services_phone' => SiteSetting::getValue('dealer_sales_phone', '') ?: SiteSetting::getValue('dealer_phone', ''),
            'services_hours' => SiteSetting::getValue('dealer_sales_hours', "Mon — Fri: 09:00 - 18:00\nSat: 10:00 - 16:00\nSun: Closed"),
            'studio_title' => 'Academy Office',
            'studio_address' => SiteSetting::getValue('dealer_address', 'Plot 42, Powerblink Avenue, Coastal Way, Ibeju Lekki, Lagos.'),
            'map_link_url' => 'https://maps.google.com',
            'social_instagram_label' => 'Instagram',
            'social_instagram_url' => '',
            'social_twitter_label' => 'Twitter',
            'social_twitter_url' => '',
            'atmospheric_image' => 'asset/images/powerblink/contact-us-powerblink-fc-033.jpg',
            'atmospheric_quote' => 'The distance between dreams and reality is called discipline. Our goal is to provide the bridge for every young talent in Nigeria.',
        ]);

        return view('pages.powerblink.contact', [
            'title' => $page->title.' | '.$siteName,
            'metaDescription' => $page->meta_description,
            'canonicalUrl' => route('contact', [], true),
            'ogTitle' => $page->title,
            'ogDescription' => $page->meta_description,
            'ogUrl' => route('contact', [], true),
            'ogImage' => PlaceholderMedia::url($contactSections['atmospheric_image'] ?? ''),
            'page' => $page,
            'sections' => $contactSections,
        ]);
    }

    public function faq()
    {
        $page = CmsPage::query()->where('slug', 'faq')->where('is_active', true)->firstOrFail();
        $siteName = SiteBrand::displayName();
        $sections = $this->pageSections('faq', [
            'kicker' => 'Need Help?',
            'heading' => 'HELP CENTER',
            'intro' => 'Common questions about registration, training, and academy policies.',
            'hero_image' => 'asset/images/powerblink/home-powerblink-fc-044.jpg',
            'cat_1_title' => 'Registration',
            'cat_1_icon' => 'how_to_reg',
            'cat_1_faqs' => json_encode([
                ['q' => 'When can I pay the registration fee?', 'a' => 'Payment is only available after your application is approved. You will receive an email with a secure payment link.'],
                ['q' => 'How long does review take?', 'a' => 'Our team typically reviews applications within a few business days.'],
            ]),
            'cat_2_title' => 'Programs',
            'cat_2_icon' => 'sports_soccer',
            'cat_2_faqs' => json_encode([
                ['q' => 'What age groups do you serve?', 'a' => 'We offer pathways from U7 through U15.'],
            ]),
            'cat_3_title' => 'Training',
            'cat_3_icon' => 'calendar_month',
            'cat_3_faqs' => json_encode([
                ['q' => 'How often do teams train?', 'a' => 'Frequency depends on the program — typically 2–4 sessions per week.'],
            ]),
            'cat_4_title' => 'Medical',
            'cat_4_icon' => 'health_and_safety',
            'cat_4_faqs' => json_encode([
                ['q' => 'What medical information is required?', 'a' => 'Please disclose allergies, relevant medical history, and fitness clearance during registration.'],
            ]),
            'cta_title' => 'STILL HAVE QUESTIONS?',
            'cta_body' => 'Contact our academy office Monday through Saturday.',
            'cta_image' => 'asset/images/powerblink/contact-us-powerblink-fc-034.jpg',
        ]);

        return view('pages.powerblink.faq', [
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
        $page = $this->resolvePublicPage('privacy-policy', 'Privacy Policy', 'How we collect, use, and protect your information.');
        $siteName = SiteBrand::displayName();
        $sections = $this->pageSections('privacy-policy', [
            'heading' => 'Privacy Policy',
            'body' => "This Privacy Policy explains how PowerBlink FC collects, uses, and protects information when you use our website and registration services.\n\nWe process guardian and player information to manage registrations, training, and academy communications.\n\nWe do not sell personal information. For data requests, please use the Contact page.",
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

    public function programs()
    {
        $page = $this->resolvePublicPage('programs', 'Programs', 'Age-group pathways and academy programs.');
        $siteName = SiteBrand::displayName();

        return view('pages.powerblink.programs', [
            'title' => $page->title.' | '.$siteName,
            'metaDescription' => $page->meta_description,
            'canonicalUrl' => route('programs', [], true),
            'ogTitle' => $page->title,
            'ogDescription' => $page->meta_description,
            'ogUrl' => route('programs', [], true),
            'page' => $page,
            'programs' => Program::query()
                ->with(['season', 'heroImage'])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function coaching()
    {
        $page = $this->resolvePublicPage('coaching', 'Coaching Team', 'Meet our licensed coaching staff.');
        $siteName = SiteBrand::displayName();

        return view('pages.powerblink.coaching', [
            'title' => $page->title.' | '.$siteName,
            'metaDescription' => $page->meta_description,
            'canonicalUrl' => route('coaching', [], true),
            'ogTitle' => $page->title,
            'ogDescription' => $page->meta_description,
            'ogUrl' => route('coaching', [], true),
            'page' => $page,
            'coaches' => Coach::query()
                ->with('photo')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function gallery()
    {
        $page = $this->resolvePublicPage('gallery', 'Gallery', 'Academy moments and match highlights.');
        $siteName = SiteBrand::displayName();

        return view('pages.powerblink.gallery', [
            'title' => $page->title.' | '.$siteName,
            'metaDescription' => $page->meta_description,
            'canonicalUrl' => route('gallery', [], true),
            'ogTitle' => $page->title,
            'ogDescription' => $page->meta_description,
            'ogUrl' => route('gallery', [], true),
            'page' => $page,
            'items' => GalleryItem::query()
                ->with('media')
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get(),
        ]);
    }

    public function tournaments()
    {
        $page = $this->resolvePublicPage('tournaments', 'Tournaments', 'Competitive fixtures and academy tournaments.');
        $siteName = SiteBrand::displayName();

        return view('pages.powerblink.tournaments', [
            'title' => $page->title.' | '.$siteName,
            'metaDescription' => $page->meta_description,
            'canonicalUrl' => route('tournaments', [], true),
            'ogTitle' => $page->title,
            'ogDescription' => $page->meta_description,
            'ogUrl' => route('tournaments', [], true),
            'page' => $page,
            'tournaments' => Tournament::query()
                ->with(['season', 'featuredImage'])
                ->latest('start_date')
                ->get(),
        ]);
    }

    public function terms()
    {
        $page = $this->resolvePublicPage('terms', 'Terms & Conditions', 'Terms governing use of our academy platform.');
        $siteName = SiteBrand::displayName();
        $sections = $this->pageSections('terms', [
            'heading' => 'Terms & Conditions',
            'body' => "These Terms govern your use of the PowerBlink FC website and registration system.\n\nBy submitting a registration application, you agree to provide accurate information and comply with academy policies.\n\nProgram fees, schedules, and policies may be updated from time to time.",
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
}
