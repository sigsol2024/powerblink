<?php

namespace App\View\Composers;

use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Support\CmsNavigation;
use Illuminate\View\View;

/**
 * FAQ knowledge-base nav tiles for the public header mega menu (titles/icons match CMS defaults).
 */
class FaqNavComposer
{
    public function compose(View $view): void
    {
        if (! CmsNavigation::isVisible('faq')) {
            $view->with('faqNavItems', []);

            return;
        }

        $defaults = [
            'cat_1_title' => 'Buying & Inventory',
            'cat_1_icon' => 'directions_car',
            'cat_2_title' => 'Financing & Trade',
            'cat_2_icon' => 'payments',
            'cat_3_title' => 'Performance Service',
            'cat_3_icon' => 'build_circle',
            'cat_4_title' => 'Selling to Apex',
            'cat_4_icon' => 'sell',
        ];

        $stored = [];
        try {
            $stored = PageSection::query()
                ->where('page', 'faq')
                ->pluck('content', 'section_key')
                ->all();
        } catch (\Throwable) {
            $stored = [];
        }

        $items = [];
        foreach ([1, 2, 3, 4] as $i) {
            $titleKey = 'cat_'.$i.'_title';
            $iconKey = 'cat_'.$i.'_icon';
            $title = trim((string) ($stored[$titleKey] ?? SiteSetting::getValue('page_faq_'.$titleKey, $defaults[$titleKey] ?? '')));
            if ($title === '') {
                continue;
            }
            $icon = trim((string) ($stored[$iconKey] ?? SiteSetting::getValue('page_faq_'.$iconKey, $defaults[$iconKey] ?? 'help')));
            $items[] = [
                'idx' => $i,
                'title' => $title,
                'icon' => $icon !== '' ? $icon : 'help',
                'hash' => 'cat-'.$i,
            ];
        }

        $view->with('faqNavItems', $items);
    }
}
