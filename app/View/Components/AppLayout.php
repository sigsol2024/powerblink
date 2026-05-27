<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Pages with sticky save bars, canvases, or full-bleed visuals (e.g. the product
     * create/edit form) set this to true to bypass the standard max-width wrapper.
     */
    public bool $fullBleed;

    public function __construct(bool $fullBleed = false)
    {
        $this->fullBleed = $fullBleed;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        if (Auth::check()) {
            return view('layouts.admin');
        }

        return view('layouts.app');
    }
}
