<?php

namespace App\View\Composers;

use App\Support\CmsNavigation;
use Illuminate\View\View;

class CmsNavComposer
{
    public function compose(View $view): void
    {
        $view->with('cmsNavActive', CmsNavigation::visibilityMap());
    }
}
