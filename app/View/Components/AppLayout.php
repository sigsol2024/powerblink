<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Pages with sticky save bars, canvases, or full-bleed visuals set this to true
     * to bypass the standard max-width wrapper.
     */
    public bool $fullBleed;

    public function __construct(bool $fullBleed = false)
    {
        $this->fullBleed = $fullBleed;
    }

    public function render(): View
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->canAccessAdminPanel()) {
                return view('layouts.admin-portal');
            }

            if ($user->isCoach() || $user->isParent() || $user->isPlayer()) {
                return view('layouts.member-portal');
            }
        }

        return view('layouts.app');
    }
}
