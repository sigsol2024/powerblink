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
                'default_description' => 'Homepage hero, mission, and program highlights for PowerBlink FC.',
                'fields' => [
                    ['name' => 'hero_eyebrow', 'label' => 'Hero eyebrow', 'type' => 'text', 'default' => 'ELITE ACADEMY TRIALS OPEN', 'group' => 'Hero'],
                    ['name' => 'hero_title', 'label' => 'Hero title', 'type' => 'text', 'default' => "Developing Tomorrow's Football Stars Today", 'group' => 'Hero'],
                    ['name' => 'hero_subtitle', 'label' => 'Hero subtitle', 'type' => 'textarea', 'default' => 'Structured football development for players aged 7–15 through elite coaching, competitive tournaments, and professional mentorship in Ibeju Lekki.', 'group' => 'Hero'],
                    ['name' => 'hero_cta_text', 'label' => 'Hero CTA text', 'type' => 'text', 'default' => 'Register Now', 'group' => 'Hero'],
                    ['name' => 'hero_cta_href', 'label' => 'Hero CTA link', 'type' => 'text', 'default' => '/register', 'group' => 'Hero'],
                    ['name' => 'hero_image', 'label' => 'Hero image', 'type' => 'image', 'default' => 'asset/images/powerblink/home-powerblink-fc-044.jpg', 'group' => 'Hero', 'preview' => 'thumbnail'],
                    ['name' => 'about_preview_image', 'label' => 'About preview image', 'type' => 'image', 'default' => 'asset/images/powerblink/home-powerblink-fc-045.jpg', 'group' => 'Hero', 'preview' => 'thumbnail'],
                    ['name' => 'shop_categories_title', 'label' => 'Programs section title', 'type' => 'text', 'default' => 'Development Programs', 'group' => 'Programs section'],
                    ['name' => 'promo_eyebrow', 'label' => 'Promo eyebrow', 'type' => 'text', 'default' => 'SEASON REGISTRATION', 'group' => 'Promo banner'],
                    ['name' => 'promo_title', 'label' => 'Promo title', 'type' => 'text', 'default' => 'Ready To Begin Your Football Journey?', 'group' => 'Promo banner'],
                    ['name' => 'promo_cta', 'label' => 'Promo button text', 'type' => 'text', 'default' => 'Register Today', 'group' => 'Promo banner'],
                    ['name' => 'welcome_eyebrow', 'label' => 'Mission eyebrow', 'type' => 'text', 'default' => 'OUR MISSION', 'group' => 'Mission block'],
                    ['name' => 'welcome_title', 'label' => 'Mission title', 'type' => 'text', 'default' => 'Elite Excellence in Ibeju Lekki', 'group' => 'Mission block'],
                    ['name' => 'welcome_body', 'label' => 'Mission body', 'type' => 'textarea', 'default' => 'Powerblink Football Club Limited is a launchpad for dreams — a safe, world-class environment where young athletes transform raw passion into professional competence.', 'group' => 'Mission block'],
                ],
            ],
            'about' => [
                'label' => 'About',
                'default_title' => 'About Us',
                'default_description' => 'About page hero, story, coaching spotlight, and values.',
                'fields' => [
                    ['name' => 'hero_image', 'label' => 'Hero image', 'type' => 'image', 'default' => 'asset/images/powerblink/about-us-powerblink-fc-001.jpg', 'group' => 'Hero', 'preview' => 'thumbnail'],
                    ['name' => 'hero_title', 'label' => 'Hero headline', 'type' => 'text', 'default' => 'Our Story', 'group' => 'Hero'],
                    ['name' => 'philosophy_kicker', 'label' => 'Philosophy kicker', 'type' => 'text', 'default' => 'Our Philosophy', 'group' => 'Our story'],
                    ['name' => 'philosophy_title', 'label' => 'Philosophy title', 'type' => 'text', 'default' => 'Excellence On and Off the Pitch', 'group' => 'Our story'],
                    ['name' => 'philosophy_quote', 'label' => 'Philosophy quote', 'type' => 'textarea', 'default' => '"We develop players who compete with courage, discipline, and joy."', 'group' => 'Our story'],
                    ['name' => 'story_paragraph_1', 'label' => 'Story paragraph 1', 'type' => 'textarea', 'default' => 'PowerBlink FC is a youth football academy committed to holistic player development.', 'group' => 'Our story'],
                    ['name' => 'story_paragraph_2', 'label' => 'Story paragraph 2', 'type' => 'textarea', 'default' => 'Our programs blend technical training, match intelligence, and character building for athletes aged U7 through U15.', 'group' => 'Our story'],
                    ['name' => 'story_cta_text', 'label' => 'Story CTA text', 'type' => 'text', 'default' => 'View Programs', 'group' => 'Our story'],
                    ['name' => 'story_cta_href', 'label' => 'Story CTA link', 'type' => 'text', 'default' => '/register', 'group' => 'Our story'],
                    ['name' => 'artisan_kicker', 'label' => 'Coaches kicker', 'type' => 'text', 'default' => 'Our Coaches', 'group' => 'Coaches'],
                    ['name' => 'artisan_title', 'label' => 'Coaches title', 'type' => 'text', 'default' => 'Licensed, Experienced Staff', 'group' => 'Coaches'],
                    ['name' => 'artisan_body', 'label' => 'Coaches body', 'type' => 'textarea', 'default' => 'Our coaching team brings UEFA and CAF qualifications alongside years of academy experience.', 'group' => 'Coaches'],
                    ['name' => 'artisan_image', 'label' => 'Coaches image', 'type' => 'image', 'default' => 'asset/images/powerblink/about-us-powerblink-fc-009.jpg', 'group' => 'Coaches', 'preview' => 'thumbnail'],
                    ['name' => 'artisan_location_label', 'label' => 'Location label', 'type' => 'text', 'default' => 'Location', 'group' => 'Coaches'],
                    ['name' => 'artisan_location_detail', 'label' => 'Location detail', 'type' => 'text', 'default' => 'Lagos, Nigeria', 'group' => 'Coaches'],
                    ['name' => 'values_title', 'label' => 'Core values heading', 'type' => 'text', 'default' => 'Core Values', 'group' => 'Core values'],
                    ['name' => 'val_1_title', 'label' => 'Value 1 title', 'type' => 'text', 'default' => 'Discipline', 'group' => 'Core values'],
                    ['name' => 'val_1_body', 'label' => 'Value 1 body', 'type' => 'textarea', 'default' => 'Consistent effort and respect for teammates, coaches, and the game.', 'group' => 'Core values'],
                    ['name' => 'val_2_title', 'label' => 'Value 2 title', 'type' => 'text', 'default' => 'Development', 'group' => 'Core values'],
                    ['name' => 'val_2_body', 'label' => 'Value 2 body', 'type' => 'textarea', 'default' => 'Age-appropriate training that challenges players to grow every session.', 'group' => 'Core values'],
                    ['name' => 'val_3_title', 'label' => 'Value 3 title', 'type' => 'text', 'default' => 'Community', 'group' => 'Core values'],
                    ['name' => 'val_3_body', 'label' => 'Value 3 body', 'type' => 'textarea', 'default' => 'Families, players, and staff united around shared goals.', 'group' => 'Core values'],
                    ['name' => 'newsletter_title', 'label' => 'Newsletter title', 'type' => 'text', 'default' => 'Stay Connected', 'group' => 'Newsletter'],
                    ['name' => 'newsletter_body', 'label' => 'Newsletter body', 'type' => 'textarea', 'default' => 'Receive academy news and registration updates.', 'group' => 'Newsletter'],
                ],
            ],
            'contact' => [
                'label' => 'Contact',
                'default_title' => 'Contact Us',
                'default_description' => 'Contact page hero, office details, and imagery.',
                'fields' => [
                    ['name' => 'hero_title', 'label' => 'Hero title', 'type' => 'text', 'default' => 'Contact Powerblink FC', 'group' => 'Hero'],
                    ['name' => 'hero_intro', 'label' => 'Hero intro', 'type' => 'textarea', 'default' => 'Our academy staff are available to answer questions about programs, trials, and registration.', 'group' => 'Hero'],
                    ['name' => 'services_email', 'label' => 'Contact email', 'type' => 'text', 'default' => '', 'group' => 'Contact details'],
                    ['name' => 'services_phone', 'label' => 'Contact phone', 'type' => 'text', 'default' => '', 'group' => 'Contact details'],
                    ['name' => 'services_hours', 'label' => 'Office hours', 'type' => 'textarea', 'default' => "Mon — Fri: 09:00 - 18:00\nSat: 10:00 - 16:00\nSun: Closed", 'group' => 'Contact details'],
                    ['name' => 'studio_title', 'label' => 'Office section title', 'type' => 'text', 'default' => 'Academy Office', 'group' => 'Office'],
                    ['name' => 'studio_address', 'label' => 'Office address', 'type' => 'textarea', 'default' => '', 'group' => 'Office'],
                    ['name' => 'map_link_url', 'label' => 'Map link URL', 'type' => 'text', 'default' => 'https://maps.google.com', 'group' => 'Office'],
                    ['name' => 'social_instagram_label', 'label' => 'Instagram label', 'type' => 'text', 'default' => 'Instagram', 'group' => 'Social'],
                    ['name' => 'social_instagram_url', 'label' => 'Instagram URL', 'type' => 'text', 'default' => '', 'group' => 'Social'],
                    ['name' => 'social_twitter_label', 'label' => 'Second social label', 'type' => 'text', 'default' => 'Twitter', 'group' => 'Social'],
                    ['name' => 'social_twitter_url', 'label' => 'Second social URL', 'type' => 'text', 'default' => '', 'group' => 'Social'],
                    ['name' => 'atmospheric_image', 'label' => 'Side image', 'type' => 'image', 'default' => 'asset/images/powerblink/contact-us-powerblink-fc-033.jpg', 'group' => 'Imagery', 'preview' => 'thumbnail'],
                    ['name' => 'atmospheric_quote', 'label' => 'Quote', 'type' => 'text', 'default' => 'The distance between dreams and reality is called discipline.', 'group' => 'Imagery'],
                ],
            ],
            'faq' => [
                'label' => 'FAQ',
                'default_title' => 'Frequently Asked Questions',
                'default_description' => 'FAQ page copy and SEO metadata.',
                'fields' => [
                    ['name' => 'kicker', 'label' => 'Header kicker', 'type' => 'text', 'default' => 'Need Help?', 'group' => 'Page hero'],
                    ['name' => 'heading', 'label' => 'Header title', 'type' => 'text', 'default' => 'HELP CENTER', 'group' => 'Page hero'],
                    ['name' => 'intro', 'label' => 'Header intro', 'type' => 'textarea', 'default' => 'Common questions about registration, training, and academy policies.', 'group' => 'Page hero'],
                    ['name' => 'hero_image', 'label' => 'Hero image', 'type' => 'image', 'default' => 'asset/images/powerblink/home-powerblink-fc-044.jpg', 'group' => 'Media', 'preview' => 'thumbnail'],
                    ['name' => 'cat_1_title', 'label' => 'Category 1 title', 'type' => 'text', 'default' => 'Registration', 'group' => 'Category 1'],
                    ['name' => 'cat_1_icon', 'label' => 'Category 1 icon', 'type' => 'text', 'default' => 'how_to_reg', 'group' => 'Category 1'],
                    ['name' => 'cat_1_faqs', 'label' => 'Category 1 FAQs', 'type' => 'repeater', 'default' => '[]', 'group' => 'Category 1', 'schema' => [
                        ['name' => 'q', 'label' => 'Question', 'type' => 'text'],
                        ['name' => 'a', 'label' => 'Answer', 'type' => 'textarea'],
                    ]],
                    ['name' => 'cat_2_title', 'label' => 'Category 2 title', 'type' => 'text', 'default' => 'Programs', 'group' => 'Category 2'],
                    ['name' => 'cat_2_icon', 'label' => 'Category 2 icon', 'type' => 'text', 'default' => 'sports_soccer', 'group' => 'Category 2'],
                    ['name' => 'cat_2_faqs', 'label' => 'Category 2 FAQs', 'type' => 'repeater', 'default' => '[]', 'group' => 'Category 2', 'schema' => [
                        ['name' => 'q', 'label' => 'Question', 'type' => 'text'],
                        ['name' => 'a', 'label' => 'Answer', 'type' => 'textarea'],
                    ]],
                    ['name' => 'cat_3_title', 'label' => 'Category 3 title', 'type' => 'text', 'default' => 'Training', 'group' => 'Category 3'],
                    ['name' => 'cat_3_icon', 'label' => 'Category 3 icon', 'type' => 'text', 'default' => 'calendar_month', 'group' => 'Category 3'],
                    ['name' => 'cat_3_faqs', 'label' => 'Category 3 FAQs', 'type' => 'repeater', 'default' => '[]', 'group' => 'Category 3', 'schema' => [
                        ['name' => 'q', 'label' => 'Question', 'type' => 'text'],
                        ['name' => 'a', 'label' => 'Answer', 'type' => 'textarea'],
                    ]],
                    ['name' => 'cat_4_title', 'label' => 'Category 4 title', 'type' => 'text', 'default' => 'Medical', 'group' => 'Category 4'],
                    ['name' => 'cat_4_icon', 'label' => 'Category 4 icon', 'type' => 'text', 'default' => 'health_and_safety', 'group' => 'Category 4'],
                    ['name' => 'cat_4_faqs', 'label' => 'Category 4 FAQs', 'type' => 'repeater', 'default' => '[]', 'group' => 'Category 4', 'schema' => [
                        ['name' => 'q', 'label' => 'Question', 'type' => 'text'],
                        ['name' => 'a', 'label' => 'Answer', 'type' => 'textarea'],
                    ]],
                    ['name' => 'cta_title', 'label' => 'CTA title', 'type' => 'text', 'default' => 'STILL HAVE QUESTIONS?', 'group' => 'CTA section'],
                    ['name' => 'cta_body', 'label' => 'CTA body', 'type' => 'textarea', 'default' => 'Contact our academy office Monday through Saturday.', 'group' => 'CTA section'],
                    ['name' => 'cta_image', 'label' => 'CTA image', 'type' => 'image', 'default' => 'asset/images/powerblink/contact-us-powerblink-fc-034.jpg', 'group' => 'CTA section', 'preview' => 'thumbnail'],
                ],
            ],
            'privacy-policy' => [
                'label' => 'Privacy Policy',
                'default_title' => 'Privacy Policy',
                'default_description' => 'Privacy policy content for academy families.',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Page heading', 'type' => 'text', 'default' => 'Privacy Policy', 'group' => 'Content'],
                    ['name' => 'body', 'label' => 'Policy body', 'type' => 'textarea', 'default' => "This Privacy Policy explains how PowerBlink FC collects, uses, and protects information when you use our website and registration services.\n\nWe process guardian and player information to manage registrations, training, and academy communications.\n\nWe do not sell personal information. For data requests, please use the Contact page.", 'group' => 'Content'],
                ],
            ],
            'terms' => [
                'label' => 'Terms & Conditions',
                'default_title' => 'Terms & Conditions',
                'default_description' => 'Terms governing use of the academy platform.',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Page heading', 'type' => 'text', 'default' => 'Terms & Conditions', 'group' => 'Content'],
                    ['name' => 'body', 'label' => 'Terms body', 'type' => 'textarea', 'default' => "These Terms govern your use of the PowerBlink FC website and registration system.\n\nBy submitting a registration application, you agree to provide accurate information and comply with academy policies.\n\nProgram fees, schedules, and policies may be updated from time to time.", 'group' => 'Content'],
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

        if ($slug === 'about') {
            $data['content_html'] = '';
        }

        if (! empty($data['content_html'])) {
            $data['content_html'] = strip_tags(
                (string) $data['content_html'],
                '<p><br><strong><em><ul><ol><li><a><h2><h3><h4>'
            );
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
            $content = is_string($value) ? $value : (string) $value;
            if (in_array($field['type'], ['text', 'textarea'], true)) {
                $content = strip_tags($content);
            }
            PageSection::query()->updateOrCreate(
                [
                    'page' => $slug,
                    'section_key' => $name,
                ],
                [
                    'content_type' => $field['type'],
                    'content' => $content,
                ]
            );
        }

        return redirect()
            ->route('admin.pages.edit', ['slug' => $slug])
            ->with('status', 'Page updated successfully.');
    }
}

