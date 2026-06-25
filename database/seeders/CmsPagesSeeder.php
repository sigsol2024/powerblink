<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class CmsPagesSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            'home' => [
                'title' => 'Home',
                'meta_description' => 'PowerBlink FC — elite youth football academy in Ibeju Lekki, Lagos.',
            ],
            'about' => [
                'title' => 'About Us',
                'meta_description' => 'Our story, values, and coaching philosophy.',
            ],
            'programs' => [
                'title' => 'Programs',
                'meta_description' => 'Age-group pathways and academy programs.',
            ],
            'coaching' => [
                'title' => 'Coaching Team',
                'meta_description' => 'Meet our licensed coaching staff.',
            ],
            'gallery' => [
                'title' => 'Gallery',
                'meta_description' => 'Academy moments and match highlights.',
            ],
            'tournaments' => [
                'title' => 'Tournaments',
                'meta_description' => 'Competitive fixtures and academy tournaments.',
            ],
            'faq' => [
                'title' => 'FAQ',
                'meta_description' => 'Common questions about registration, training, and academy policies.',
            ],
            'contact' => [
                'title' => 'Contact Us',
                'meta_description' => 'Reach our academy team for registration and program inquiries.',
            ],
            'privacy-policy' => [
                'title' => 'Privacy Policy',
                'meta_description' => 'How we collect, use, and protect your information.',
            ],
            'terms' => [
                'title' => 'Terms & Conditions',
                'meta_description' => 'Terms governing use of our academy platform.',
            ],
        ];

        foreach ($definitions as $slug => $def) {
            CmsPage::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $def['title'],
                    'meta_description' => $def['meta_description'],
                    'content_html' => '',
                    'is_active' => true,
                ]
            );
        }
    }
}
