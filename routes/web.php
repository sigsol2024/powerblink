<?php

use App\Http\Controllers\AdminAnalyticsController;
use App\Http\Controllers\AdminListingOptionController;
use App\Http\Controllers\AdminMediaController;
use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\AdminSiteSettingsController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminVehicleController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CurrencyPreferenceController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ListingOptionLookupController;
use App\Http\Controllers\MediaLibraryController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicStorageMediaController;
use App\Http\Controllers\TemporaryAdminController;
use App\Http\Controllers\UserVehicleController;
use App\Http\Controllers\VehicleInquiryController;
use App\Http\Controllers\VendorSettingsController;
use App\Models\AdminAuditTrail;
use App\Models\SiteTrafficEvent;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

if (app()->environment('local')) {
    Route::get('/asset-smoke-test', function () {
        return view('pages.asset-smoke-test');
    });
}

if (config('app.admin_bootstrap_enabled')) {
    Route::middleware('guest')->group(function () {
        Route::get('/bootstrap-admin', [TemporaryAdminController::class, 'create'])->name('bootstrap.admin');
        Route::post('/bootstrap-admin', [TemporaryAdminController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('bootstrap.admin.store');
    });
}

// There is no separate admin login URL; admins use the same session as other users.
Route::redirect('/admin/login', '/login', 302);

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::post('/contact', [ContactController::class, 'submit'])->middleware('throttle:5,1')->name('contact.submit');

Route::middleware('auth')->group(function () {
    Route::get('/auth/google/welcome', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'welcome'])->name('auth.google.welcome');
});
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->middleware('throttle:10,1')
    ->name('newsletter.subscribe');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/inventory', [PageController::class, 'inventory'])->name('inventory.index');
Route::post('/inventory/{slug}/inquiry', [VehicleInquiryController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('inventory.inquiry');
Route::get('/inventory/{slug?}', [PageController::class, 'vehicleShow'])->name('inventory.show');
Route::get('/compare', [PageController::class, 'compare'])->name('compare');
Route::post('/currency/select', [CurrencyPreferenceController::class, 'update'])
    ->middleware('throttle:20,1')
    ->name('currency.select');

Route::post('/compare/add/{vehicle}', [CompareController::class, 'add'])->name('compare.add');
Route::post('/compare/remove/{vehicle}', [CompareController::class, 'remove'])->name('compare.remove');
Route::post('/compare/clear', [CompareController::class, 'clear'])->name('compare.clear');
Route::get('/media/storage/{path}', [PublicStorageMediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.storage.show');

Route::get('/dashboard', function (Request $request) {
    $user = $request->user();

    if ($user->hasRole('admin')) {
        return view('dashboard', [
            'stats' => [
                'total' => Vehicle::query()->count(),
                'pending' => Vehicle::query()->where('status', 'pending')->count(),
                'approved' => Vehicle::query()->where('status', 'approved')->count(),
            ],
            'adminOverview' => true,
        ]);
    }

    return view('dashboard', [
        'stats' => [
            'total' => $user->vehicles()->count(),
            'pending' => $user->vehicles()->where('status', 'pending')->count(),
            'approved' => $user->vehicles()->where('status', 'approved')->count(),
        ],
        'adminOverview' => false,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/favorites', [FavoriteController::class, 'index'])->name('dashboard.favorites.index');
    Route::post('/favorites/{vehicle}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('dashboard')->group(function () {
        Route::get('/seller-profile', [VendorSettingsController::class, 'edit'])->name('dashboard.vendor-settings.edit');
        Route::put('/seller-profile', [VendorSettingsController::class, 'update'])->name('dashboard.vendor-settings.update');

        Route::get('/vehicles', [UserVehicleController::class, 'index'])->name('dashboard.vehicles.index');
        Route::get('/vehicles/create', [UserVehicleController::class, 'create'])->name('dashboard.vehicles.create');
        Route::post('/vehicles', [UserVehicleController::class, 'store'])->name('dashboard.vehicles.store');
        Route::get('/vehicles/{vehicle}/edit', [UserVehicleController::class, 'edit'])->name('dashboard.vehicles.edit');
        Route::put('/vehicles/{vehicle}', [UserVehicleController::class, 'update'])->name('dashboard.vehicles.update');
        Route::post('/vehicles/{vehicle}/submit', [UserVehicleController::class, 'submit'])->name('dashboard.vehicles.submit');
        Route::delete('/vehicles/{vehicle}', [UserVehicleController::class, 'destroy'])->name('dashboard.vehicles.destroy');
        Route::delete('/vehicles/{vehicle}/images/{image}', [UserVehicleController::class, 'destroyImage'])->name('dashboard.vehicles.images.destroy');
        // Some hosting/WAF setups block DELETE requests; accept POST as well.
        Route::post('/vehicles/{vehicle}/images/{image}', [UserVehicleController::class, 'destroyImage'])->name('dashboard.vehicles.images.destroy.post');
    });

    Route::get('/dashboard/listing-models/{make}', [ListingOptionLookupController::class, 'modelsForMake'])
        ->whereNumber('make')
        ->name('dashboard.listing-models');

    Route::get('/dashboard/api/media', [MediaLibraryController::class, 'list'])->name('dashboard.api.media');
    Route::post('/dashboard/api/media', [MediaLibraryController::class, 'upload'])->name('dashboard.api.media.upload');
});

Route::middleware(['auth', 'role:admin', 'admin.audit'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        $analyticsStart = now()->subDays(89)->startOfDay();
        $analyticsEnd = now();
        $analyticsBase = SiteTrafficEvent::query()->betweenDates($analyticsStart, $analyticsEnd);
        $topPage = (clone $analyticsBase)->selectRaw('path, COUNT(*) as views')->groupBy('path')->orderByDesc('views')->first();
        $topPagePath = trim((string) ($topPage->path ?? ''));
        $topPageLabel = 'No data yet';
        if ($topPagePath !== '') {
            if ($topPagePath === '/') {
                $topPageLabel = 'Homepage';
            } elseif (str_starts_with($topPagePath, '/inventory/')) {
                $topPageLabel = 'Listing detail';
            } else {
                $clean = trim(str_replace(['-', '_', '/'], ' ', $topPagePath));
                $topPageLabel = ucwords($clean !== '' ? $clean : $topPagePath);
            }
        }

        $auditStart = now()->subDays(30)->startOfDay();
        $auditBase = AdminAuditTrail::query()->where('created_at', '>=', $auditStart);

        return view('admin.dashboard', [
            'stats' => [
                'total_listings' => Vehicle::query()->count(),
                'pending_listings' => Vehicle::query()->where('status', 'pending')->count(),
                'approved_listings' => Vehicle::query()->where('status', 'approved')->count(),
                'users_count' => User::query()->count(),
            ],
            'analyticsSummary' => [
                'range_days' => 90,
                'total_views' => (clone $analyticsBase)->count(),
                'unique_sessions' => (clone $analyticsBase)->whereNotNull('session_id')->distinct('session_id')->count('session_id'),
                'top_page' => $topPage,
                'top_page_label' => $topPageLabel,
                'top_listing' => (clone $analyticsBase)->whereNotNull('vehicle_slug')->selectRaw('vehicle_slug, COUNT(*) as views')->groupBy('vehicle_slug')->orderByDesc('views')->first(),
            ],
            'auditSummary' => [
                'range_days' => 30,
                'total_actions' => (clone $auditBase)->count(),
                'create_actions' => (clone $auditBase)->whereIn('method', ['POST'])->count(),
                'update_actions' => (clone $auditBase)->whereIn('method', ['PUT', 'PATCH'])->count(),
                'delete_actions' => (clone $auditBase)->where('method', 'DELETE')->count(),
                'recent' => (clone $auditBase)->with('user:id,name,email')->latest()->take(8)->get(),
            ],
        ]);
    })->name('admin.dashboard');

    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('admin.analytics.index');
    Route::get('/analytics/data', [AdminAnalyticsController::class, 'data'])->name('admin.analytics.data');
    Route::get('/audit', function (Request $request) {
        $filters = $request->validate([
            'method' => ['nullable', 'string', 'max:10'],
            'user_id' => ['nullable', 'integer', 'min:1'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $query = AdminAuditTrail::query()->with('user:id,name,email')->latest();

        $method = strtoupper(trim((string) ($filters['method'] ?? '')));
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $query->where('method', $method);
        } else {
            $method = '';
        }

        $userId = (int) ($filters['user_id'] ?? 0);
        if ($userId > 0) {
            $query->where('user_id', $userId);
        }

        if (! empty($filters['from'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['from'])->startOfDay());
        }
        if (! empty($filters['to'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['to'])->endOfDay());
        }

        $search = trim((string) ($filters['q'] ?? ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('path', 'like', '%'.$search.'%')
                    ->orWhere('route_name', 'like', '%'.$search.'%')
                    ->orWhere('ip_address', 'like', '%'.$search.'%');
            });
        }

        return view('admin.audit.index', [
            'entries' => $query->paginate(30)->withQueryString(),
            'method' => $method,
            'userId' => $userId,
            'from' => (string) ($filters['from'] ?? ''),
            'to' => (string) ($filters['to'] ?? ''),
            'search' => $search,
            'admins' => User::query()->role('admin')->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    })->name('admin.audit.index');

    Route::redirect('/vehicles', '/dashboard/vehicles')->name('admin.vehicles.index');
    Route::get('/vehicles/{vehicle}/edit', fn (Vehicle $vehicle) => redirect()->route('dashboard.vehicles.edit', $vehicle))->name('admin.vehicles.edit');

    Route::post('/vehicles/{vehicle}/approve', [AdminVehicleController::class, 'approve'])->name('admin.vehicles.approve');
    Route::post('/vehicles/{vehicle}/reject', [AdminVehicleController::class, 'reject'])->name('admin.vehicles.reject');

    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/pages', [AdminPageController::class, 'index'])->name('admin.pages.index');
    Route::get('/pages/{slug}/edit', [AdminPageController::class, 'edit'])->name('admin.pages.edit');
    Route::put('/pages/{slug}', [AdminPageController::class, 'update'])->name('admin.pages.update');
    Route::get('/media', [AdminMediaController::class, 'index'])->name('admin.media.index');
    Route::post('/media', [MediaLibraryController::class, 'upload'])->name('admin.media.upload');
    Route::delete('/media/{media}', [AdminMediaController::class, 'destroy'])->name('admin.media.destroy');
    Route::post('/media/{media}', [AdminMediaController::class, 'destroy'])->name('admin.media.destroy.post');
    Route::post('/media/bulk-delete', [AdminMediaController::class, 'bulkDestroy'])->name('admin.media.bulk-destroy');
    Route::get('/api/media', [MediaLibraryController::class, 'list'])->name('admin.media.list');

    Route::get('/settings', [AdminSiteSettingsController::class, 'edit'])->name('admin.settings.edit');
    Route::put('/settings', [AdminSiteSettingsController::class, 'update'])->name('admin.settings.update');

    Route::get('/listing-options', [AdminListingOptionController::class, 'index'])->name('admin.listing-options.index');
    Route::get('/listing-options/{category}', [AdminListingOptionController::class, 'show'])->name('admin.listing-options.show');
    Route::post('/listing-options/{category}', [AdminListingOptionController::class, 'store'])->name('admin.listing-options.store');
    Route::put('/listing-options/{category}/batch', [AdminListingOptionController::class, 'batchUpdate'])->name('admin.listing-options.batch-update');
    Route::put('/listing-options/{category}/options/{option}', [AdminListingOptionController::class, 'update'])->name('admin.listing-options.update');
    Route::delete('/listing-options/{category}/options/{option}', [AdminListingOptionController::class, 'destroy'])->name('admin.listing-options.destroy');
    Route::post('/listing-options/{category}/options/{option}/move', [AdminListingOptionController::class, 'move'])->name('admin.listing-options.move');
});

require __DIR__.'/auth.php';
