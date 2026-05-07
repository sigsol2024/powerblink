<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\PageSection;
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
                'default_description' => 'Lorem ipsum homepage SEO and section copy (white-label defaults, like reference CMS).',
                'fields' => [
                    ['name' => 'hero_title', 'label' => 'Hero Title', 'type' => 'text', 'default' => 'Lorem ipsum dolor sit amet', 'group' => 'Hero & search'],
                    ['name' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'text', 'default' => 'Consectetur adipiscing elit, sed do eiusmod tempor incididunt', 'group' => 'Hero & search'],
                    ['name' => 'hero_description', 'label' => 'Hero Description (small line)', 'type' => 'textarea', 'default' => '', 'group' => 'Hero & search'],
                    ['name' => 'hero_cta_text', 'label' => 'Hero CTA Button Text', 'type' => 'text', 'default' => 'Lorem CTA', 'group' => 'Hero & search'],
                    ['name' => 'hero_cta_href', 'label' => 'Hero CTA Link (path or URL)', 'type' => 'text', 'default' => '/inventory', 'group' => 'Hero & search'],
                    ['name' => 'home_search_label', 'label' => 'Search Bar Label', 'type' => 'text', 'default' => 'Lorem ipsum — search inventory', 'group' => 'Hero & search'],
                    ['name' => 'hero_image', 'label' => 'Hero Background Image', 'type' => 'image', 'default' => 'asset/images/media/home-hero-main.jpg', 'group' => 'Hero & search', 'preview' => 'thumbnail'],
                    ['name' => 'recent_title', 'label' => 'Featured Listings Title', 'type' => 'text', 'default' => 'Lorem dolor sit amet', 'group' => 'Featured listings'],
                    ['name' => 'recent_subtitle', 'label' => 'Featured Listings Intro', 'type' => 'textarea', 'default' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Cards below are live listings.', 'group' => 'Featured listings'],
                    ['name' => 'dealer_cta_bg', 'label' => 'Dealer CTA section background', 'type' => 'image', 'default' => 'asset/images/media/home-cta-left.jpg', 'group' => 'Dealer CTA', 'preview' => 'thumbnail'],
                    ['name' => 'dealer_cta_left_icon', 'label' => 'Left card Material icon name', 'type' => 'text', 'default' => 'directions_car', 'group' => 'Dealer CTA'],
                    ['name' => 'dealer_cta_right_icon', 'label' => 'Right card Material icon name', 'type' => 'text', 'default' => 'sell', 'group' => 'Dealer CTA'],
                    ['name' => 'cta_left_title', 'label' => 'Left card title', 'type' => 'text', 'default' => 'Looking for a car?', 'group' => 'Dealer CTA'],
                    ['name' => 'cta_left_body', 'label' => 'Left card body', 'type' => 'textarea', 'default' => 'Our cars are delivered fully-registered with all requirements completed. We\'ll deliver your car wherever you are.', 'group' => 'Dealer CTA'],
                    ['name' => 'cta_left_button_text', 'label' => 'Left button text', 'type' => 'text', 'default' => 'Inventory', 'group' => 'Dealer CTA'],
                    ['name' => 'cta_left_button_href', 'label' => 'Left button link (path or URL)', 'type' => 'text', 'default' => '/inventory', 'group' => 'Dealer CTA'],
                    ['name' => 'cta_right_title', 'label' => 'Right card title', 'type' => 'text', 'default' => 'Want to sell a car?', 'group' => 'Dealer CTA'],
                    ['name' => 'cta_right_body', 'label' => 'Right card body', 'type' => 'textarea', 'default' => 'Receive the absolute best value for your trade-in vehicle. We even handle all paperwork. Schedule appointment!', 'group' => 'Dealer CTA'],
                    ['name' => 'cta_right_button_text', 'label' => 'Right button text', 'type' => 'text', 'default' => 'Sell your car', 'group' => 'Dealer CTA'],
                    ['name' => 'cta_right_button_href', 'label' => 'Right button link (path or URL)', 'type' => 'text', 'default' => '/register', 'group' => 'Dealer CTA'],
                    ['name' => 'feat1_title', 'label' => 'Feature 1 Title', 'type' => 'text', 'default' => 'Lorem ipsum', 'group' => 'Feature highlights'],
                    ['name' => 'feat1_body', 'label' => 'Feature 1 Body', 'type' => 'textarea', 'default' => 'Dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero.', 'group' => 'Feature highlights'],
                    ['name' => 'feat2_title', 'label' => 'Feature 2 Title', 'type' => 'text', 'default' => 'Dolor sit amet', 'group' => 'Feature highlights'],
                    ['name' => 'feat2_body', 'label' => 'Feature 2 Body', 'type' => 'textarea', 'default' => 'Sed cursus ante dapibus diam. Sed nisi. Nulla quis sem at nibh elementum imperdiet.', 'group' => 'Feature highlights'],
                    ['name' => 'feat3_title', 'label' => 'Feature 3 Title', 'type' => 'text', 'default' => 'Consectetur elit', 'group' => 'Feature highlights'],
                    ['name' => 'feat3_body', 'label' => 'Feature 3 Body', 'type' => 'textarea', 'default' => 'Fusce nec tellus sed augue semper porta. Mauris massa. Vestibulum lacinia arcu eget nulla.', 'group' => 'Feature highlights'],
                    ['name' => 'welcome_title', 'label' => 'Welcome Block Title', 'type' => 'text', 'default' => 'Lorem ipsum welcome block', 'group' => 'Welcome block'],
                    ['name' => 'welcome_body', 'label' => 'Welcome Block Body', 'type' => 'textarea', 'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis sagittis ipsum. Praesent mauris. Fusce nec tellus sed augue semper porta.', 'group' => 'Welcome block'],
                    ['name' => 'welcome_video_url', 'label' => 'Welcome video (YouTube URL or ID)', 'type' => 'text', 'default' => '', 'group' => 'Welcome block'],
                    // Statistics block + Testimonial block removed (plan requirement).
                    ['name' => 'prefooter_title', 'label' => 'Pre-footer heading', 'type' => 'text', 'default' => 'Lorem ipsum — questions?', 'group' => 'Pre-footer CTA'],
                    ['name' => 'prefooter_button_text', 'label' => 'Pre-footer button text', 'type' => 'text', 'default' => 'Contact', 'group' => 'Pre-footer CTA'],
                    ['name' => 'prefooter_button_href', 'label' => 'Pre-footer button link (path or URL)', 'type' => 'text', 'default' => '/contact', 'group' => 'Pre-footer CTA'],
                ],
            ],
            'inventory' => [
                'label' => 'Inventory',
                'default_title' => 'Inventory',
                'default_description' => 'Inventory page heading and SEO. Listing cards are always loaded from approved vehicles.',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Inventory Heading', 'type' => 'text', 'default' => 'Vehicles For Sale', 'group' => 'Page header'],
                    ['name' => 'fallback_image', 'label' => 'Listing Card Fallback Image', 'type' => 'image', 'default' => 'asset/images/media/inventory-listing-fallback.jpg', 'group' => 'Media', 'preview' => 'thumbnail'],
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
                    ['name' => 'established_year', 'label' => 'Established Year Text', 'type' => 'text', 'default' => '1999', 'group' => 'Hero'],
                    ['name' => 'hero_stat_value', 'label' => 'Hero Stat Value (e.g. 25+)', 'type' => 'text', 'default' => '25+', 'group' => 'Hero'],
                    ['name' => 'hero_stat_label', 'label' => 'Hero Stat Label (e.g. Years of Excellence)', 'type' => 'text', 'default' => 'Years of Excellence', 'group' => 'Hero'],
                    ['name' => 'heading', 'label' => 'Hero Heading', 'type' => 'text', 'default' => 'WELCOME TO THE MOTORS', 'group' => 'Hero'],
                    ['name' => 'intro', 'label' => 'Hero Paragraph', 'type' => 'textarea', 'default' => 'Experience the pinnacle of automotive engineering and white-glove service. We curate the world\'s most exceptional vehicles for the discerning driver who demands nothing less than absolute mechanical perfection.', 'group' => 'Hero'],
                    ['name' => 'hero_primary_cta_text', 'label' => 'Hero Button Text', 'type' => 'text', 'default' => 'Learn Our History', 'group' => 'Hero'],
                    ['name' => 'hero_primary_cta_href', 'label' => 'Hero Button Link', 'type' => 'text', 'default' => '/about', 'group' => 'Hero'],

                    ['name' => 'values_title', 'label' => 'Core Values Heading', 'type' => 'text', 'default' => 'CORE VALUES', 'group' => 'Core values'],
                    ['name' => 'val_1_title', 'label' => 'Value 1 Title', 'type' => 'text', 'default' => 'Integrity First', 'group' => 'Core values'],
                    ['name' => 'val_1_body', 'label' => 'Value 1 Body', 'type' => 'textarea', 'default' => 'Transparent pricing and rigorous history checks for every vehicle in our showroom.', 'group' => 'Core values'],
                    ['name' => 'val_2_title', 'label' => 'Value 2 Title', 'type' => 'text', 'default' => 'Mechanical Excellence', 'group' => 'Core values'],
                    ['name' => 'val_2_body', 'label' => 'Value 2 Body', 'type' => 'textarea', 'default' => 'Our master technicians conduct a 200-point inspection ensuring performance meets factory standards.', 'group' => 'Core values'],
                    ['name' => 'val_3_title', 'label' => 'Value 3 Title', 'type' => 'text', 'default' => 'Client Concierge', 'group' => 'Core values'],
                    ['name' => 'val_3_body', 'label' => 'Value 3 Body', 'type' => 'textarea', 'default' => 'Dedicated advisors providing personalized acquisition strategies and lifelong maintenance support.', 'group' => 'Core values'],
                    ['name' => 'values_grid_1', 'label' => 'Values Grid Image 1', 'type' => 'image', 'default' => 'asset/images/media/about-values-1.jpg', 'group' => 'Core values', 'preview' => 'thumbnail'],
                    ['name' => 'values_grid_2', 'label' => 'Values Grid Image 2', 'type' => 'image', 'default' => 'asset/images/media/about-values-2.jpg', 'group' => 'Core values', 'preview' => 'thumbnail'],
                    ['name' => 'values_grid_3', 'label' => 'Values Grid Image 3', 'type' => 'image', 'default' => 'asset/images/media/about-values-3.jpg', 'group' => 'Core values', 'preview' => 'thumbnail'],
                    ['name' => 'values_grid_4', 'label' => 'Values Grid Image 4', 'type' => 'image', 'default' => 'asset/images/media/about-values-4.jpg', 'group' => 'Core values', 'preview' => 'thumbnail'],

                    ['name' => 'gallery_title', 'label' => 'Gallery title', 'type' => 'text', 'default' => 'Media Gallery', 'group' => 'Gallery'],
                    ['name' => 'gallery', 'label' => 'Media Gallery', 'type' => 'gallery', 'default' => '[]', 'group' => 'Gallery'],

                    ['name' => 'advantages_title', 'label' => 'Quick Links title', 'type' => 'text', 'default' => 'Quick Links', 'group' => 'Quick Links'],
                    ['name' => 'adv_1_icon', 'label' => 'Card 1 Icon', 'type' => 'text', 'default' => 'sell', 'group' => 'Quick Links'],
                    ['name' => 'adv_1_title', 'label' => 'Card 1 Title', 'type' => 'text', 'default' => 'Do you want to sell a car?', 'group' => 'Quick Links'],
                    ['name' => 'adv_1_body', 'label' => 'Card 1 Body', 'type' => 'textarea', 'default' => 'Get a competitive appraisal and same-day payment from our acquisition team.', 'group' => 'Quick Links'],
                    ['name' => 'adv_1_href', 'label' => 'Card 1 Link', 'type' => 'text', 'default' => '/sell', 'group' => 'Quick Links'],
                    ['name' => 'adv_2_icon', 'label' => 'Card 2 Icon', 'type' => 'text', 'default' => 'directions_car', 'group' => 'Quick Links'],
                    ['name' => 'adv_2_title', 'label' => 'Card 2 Title', 'type' => 'text', 'default' => 'Looking for a new car?', 'group' => 'Quick Links'],
                    ['name' => 'adv_2_body', 'label' => 'Card 2 Body', 'type' => 'textarea', 'default' => 'Browse our curated collection of premium inventory and certified pre-owned units.', 'group' => 'Quick Links'],
                    ['name' => 'adv_2_href', 'label' => 'Card 2 Link', 'type' => 'text', 'default' => '/inventory', 'group' => 'Quick Links'],
                    ['name' => 'adv_3_icon', 'label' => 'Card 3 Icon', 'type' => 'text', 'default' => 'build', 'group' => 'Quick Links'],
                    ['name' => 'adv_3_title', 'label' => 'Card 3 Title', 'type' => 'text', 'default' => 'Schedule a service?', 'group' => 'Quick Links'],
                    ['name' => 'adv_3_body', 'label' => 'Card 3 Body', 'type' => 'textarea', 'default' => 'Book an appointment with our specialist mechanics for maintenance or tuning.', 'group' => 'Quick Links'],
                    ['name' => 'adv_3_href', 'label' => 'Card 3 Link', 'type' => 'text', 'default' => '/service', 'group' => 'Quick Links'],

                    ['name' => 'testimonials_title', 'label' => 'Testimonials title', 'type' => 'text', 'default' => 'Customer Testimonials', 'group' => 'Testimonials'],
                    ['name' => 'testimonial_1_body', 'label' => 'Featured Testimonial Quote', 'type' => 'textarea', 'default' => 'The acquisition process for my vintage collection was handled with unparalleled professionalism. Velocity Motors doesn\'t just sell cars; they curate legacies.', 'group' => 'Testimonials'],
                    ['name' => 'testimonial_1_author', 'label' => 'Testimonial Author', 'type' => 'text', 'default' => 'John Doe', 'group' => 'Testimonials'],
                    ['name' => 'testimonial_1_brand', 'label' => 'Testimonial Author Role/Location', 'type' => 'text', 'default' => 'Private Collector, London', 'group' => 'Testimonials'],
                ],
            ],
            'faq' => [
                'label' => 'FAQ',
                'default_title' => 'Frequently Asked Questions',
                'default_description' => 'FAQ page copy and SEO metadata.',
                'fields' => [
                    ['name' => 'kicker', 'label' => 'Header Kicker', 'type' => 'text', 'default' => 'Need Help?', 'group' => 'Page hero'],
                    ['name' => 'heading', 'label' => 'Header Title', 'type' => 'text', 'default' => 'HELP CENTER', 'group' => 'Page hero'],
                    ['name' => 'intro', 'label' => 'Header Intro', 'type' => 'textarea', 'default' => 'Everything you need to know about the Apex Automotive experience, from acquisition to elite performance servicing.', 'group' => 'Page hero'],
                    ['name' => 'hero_image', 'label' => 'Hero Background Image', 'type' => 'image', 'default' => 'asset/images/media/faq-hero-bg.jpg', 'group' => 'Media', 'preview' => 'thumbnail'],

                    ['name' => 'cat_1_title', 'label' => 'Category 1 Title', 'type' => 'text', 'default' => 'Buying & Inventory', 'group' => 'Category 1: Buying'],
                    ['name' => 'cat_1_icon', 'label' => 'Category 1 Icon', 'type' => 'text', 'default' => 'directions_car', 'group' => 'Category 1: Buying'],
                    ['name' => 'cat_1_faqs', 'label' => 'Category 1 FAQs', 'type' => 'repeater', 'default' => '[]', 'group' => 'Category 1: Buying', 'schema' => [
                        ['name' => 'q', 'label' => 'Question', 'type' => 'text'],
                        ['name' => 'a', 'label' => 'Answer', 'type' => 'textarea'],
                    ]],

                    ['name' => 'cat_2_title', 'label' => 'Category 2 Title', 'type' => 'text', 'default' => 'Financing & Trade', 'group' => 'Category 2: Finance'],
                    ['name' => 'cat_2_icon', 'label' => 'Category 2 Icon', 'type' => 'text', 'default' => 'payments', 'group' => 'Category 2: Finance'],
                    ['name' => 'cat_2_faqs', 'label' => 'Category 2 FAQs', 'type' => 'repeater', 'default' => '[]', 'group' => 'Category 2: Finance', 'schema' => [
                        ['name' => 'q', 'label' => 'Question', 'type' => 'text'],
                        ['name' => 'a', 'label' => 'Answer', 'type' => 'textarea'],
                    ]],

                    ['name' => 'cat_3_title', 'label' => 'Category 3 Title', 'type' => 'text', 'default' => 'Performance Service', 'group' => 'Category 3: Service'],
                    ['name' => 'cat_3_icon', 'label' => 'Category 3 Icon', 'type' => 'text', 'default' => 'build_circle', 'group' => 'Category 3: Service'],
                    ['name' => 'cat_3_faqs', 'label' => 'Category 3 FAQs', 'type' => 'repeater', 'default' => '[]', 'group' => 'Category 3: Service', 'schema' => [
                        ['name' => 'q', 'label' => 'Question', 'type' => 'text'],
                        ['name' => 'a', 'label' => 'Answer', 'type' => 'textarea'],
                    ]],

                    ['name' => 'cat_4_title', 'label' => 'Category 4 Title', 'type' => 'text', 'default' => 'Selling to Apex', 'group' => 'Category 4: Selling'],
                    ['name' => 'cat_4_icon', 'label' => 'Category 4 Icon', 'type' => 'text', 'default' => 'sell', 'group' => 'Category 4: Selling'],
                    ['name' => 'cat_4_faqs', 'label' => 'Category 4 FAQs', 'type' => 'repeater', 'default' => '[]', 'group' => 'Category 4: Selling', 'schema' => [
                        ['name' => 'q', 'label' => 'Question', 'type' => 'text'],
                        ['name' => 'a', 'label' => 'Answer', 'type' => 'textarea'],
                    ]],

                    ['name' => 'cta_title', 'label' => 'CTA Title', 'type' => 'text', 'default' => 'STILL SEEKING ANSWERS?', 'group' => 'CTA Section'],
                    ['name' => 'cta_body', 'label' => 'CTA Body', 'type' => 'textarea', 'default' => 'Our automotive concierges are available 7 days a week to assist with technical specifications or test drives.', 'group' => 'CTA Section'],
                    ['name' => 'cta_image', 'label' => 'CTA Side Image', 'type' => 'image', 'default' => 'asset/images/media/faq-cta.jpg', 'group' => 'CTA Section', 'preview' => 'thumbnail'],
                ],
            ],
            'compare' => [
                'label' => 'Compare',
                'default_title' => 'Compare Vehicles',
                'default_description' => 'Compare page heading and intro copy.',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Compare Heading', 'type' => 'text', 'default' => 'Compare Vehicles', 'group' => 'Page intro'],
                    ['name' => 'intro', 'label' => 'Compare Intro', 'type' => 'textarea', 'default' => 'Compare list is dynamic and comes from visitor selections.', 'group' => 'Page intro'],
                ],
            ],
            'privacy-policy' => [
                'label' => 'Privacy Policy',
                'default_title' => 'Privacy Policy',
                'default_description' => 'Privacy policy content for marketplace users.',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Page heading', 'type' => 'text', 'default' => 'Privacy Policy', 'group' => 'Content'],
                    ['name' => 'body', 'label' => 'Policy body', 'type' => 'textarea', 'default' => "This Privacy Policy explains how we collect, use, and protect information when you use our marketplace website.\n\nIf you create an account, sign in (including via Google), save listings, submit inquiries, or contact us, we may process the information you provide to deliver these features.\n\nWe do not sell your personal information. We use your information to operate the site, communicate with you, prevent fraud, and comply with legal obligations.\n\nFor questions about this policy or to request access, correction, or deletion of your data, please use the Contact page.", 'group' => 'Content'],
                ],
            ],
            'terms' => [
                'label' => 'Terms & Conditions',
                'default_title' => 'Terms & Conditions',
                'default_description' => 'Terms and conditions for using the marketplace.',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Page heading', 'type' => 'text', 'default' => 'Terms & Conditions', 'group' => 'Content'],
                    ['name' => 'body', 'label' => 'Terms body', 'type' => 'textarea', 'default' => "These Terms & Conditions govern your use of our marketplace website.\n\nBy using the site, you agree not to misuse the platform, attempt unauthorized access, or submit false or misleading information.\n\nListings may be posted by dealers and by staff accounts. Listing details, pricing, and availability can change and are provided for informational purposes.\n\nIf you submit an inquiry, you agree that we may contact you using the information you provide.\n\nWe may suspend or terminate accounts that violate these terms. These terms may be updated from time to time; continued use indicates acceptance of updates.", 'group' => 'Content'],
                ],
            ],
            'listing-detail' => [
                'label' => 'Listing Detail',
                'default_title' => 'Vehicle Detail',
                'default_description' => 'Listing detail page heading and intro copy.',
                'fields' => [
                    ['name' => 'heading', 'label' => 'Listing Detail Heading', 'type' => 'text', 'default' => 'Vehicle Detail', 'group' => 'Page intro'],
                    ['name' => 'intro', 'label' => 'Listing Detail Intro', 'type' => 'textarea', 'default' => 'Vehicle details and gallery are dynamic from listing data.', 'group' => 'Page intro'],
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
