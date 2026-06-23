<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $vehicles = $request->user()
            ->favoriteVehicles()
            ->where('vehicles.status', 'approved')
            ->with(['images', 'categoryOption'])
            ->orderByDesc('vehicle_favorites.created_at')
            ->paginate(12);

        return view('pages.favorites.index', [
            'title' => 'Saved listings',
            'vehicles' => $vehicles,
        ]);
    }

    public function toggle(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $user = $request->user();

        $canSee = $vehicle->status === 'approved'
            || $user->isStaff()
            || $vehicle->user_id === $user->id;

        if (! $canSee) {
            abort(404);
        }

        if ($user->favoriteVehicles()->whereKey($vehicle->id)->exists()) {
            $user->favoriteVehicles()->detach($vehicle->id);
            $message = 'Removed from saved listings.';
        } else {
            $user->favoriteVehicles()->attach($vehicle->id);
            $message = 'Saved to your list.';
        }

        return back()->with('status', $message);
    }
}
