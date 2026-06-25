<?php

use App\Http\Controllers\AdminAnalyticsController;
use App\Http\Controllers\AdminAnnouncementController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminCoachController;
use App\Http\Controllers\AdminMediaController;
use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\AdminPerformanceReportController;
use App\Http\Controllers\AdminPlayerController;
use App\Http\Controllers\AdminProgramController;
use App\Http\Controllers\AdminRegistrationController;
use App\Http\Controllers\AdminSiteSettingsController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminTournamentController;
use App\Http\Controllers\AdminTrainingSessionController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MediaLibraryController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaystackWebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicStorageMediaController;
use App\Http\Controllers\RegistrationPaymentController;
use App\Http\Controllers\RegistrationWizardController;
use App\Http\Controllers\TemporaryAdminController;
use App\Models\AdminAuditTrail;
use App\Models\Player;
use App\Models\Registration;
use App\Models\SiteTrafficEvent;
use App\Models\User;
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

Route::redirect('/admin/login', '/login', 302);

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/programs', [PageController::class, 'programs'])->name('programs');
Route::get('/coaching', [PageController::class, 'coaching'])->name('coaching');
Route::get('/gallery', [PageController::class, 'gallery'])->name('gallery');
Route::get('/tournaments', [PageController::class, 'tournaments'])->name('tournaments');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::post('/contact', [ContactController::class, 'submit'])->middleware('throttle:5,1')->name('contact.submit');

Route::middleware('auth')->group(function () {
    Route::get('/auth/google/welcome', [GoogleAuthController::class, 'welcome'])->name('auth.google.welcome');
});
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->middleware('throttle:10,1')
    ->name('newsletter.subscribe');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

Route::post('/payment/paystack/webhook', PaystackWebhookController::class)->name('payment.paystack.webhook');
Route::get('/registration/pay/callback', [RegistrationPaymentController::class, 'callback'])->name('registration.pay.callback');

Route::middleware('registration.payment.token')->group(function () {
    Route::get('/registration/pay/{token}', [RegistrationPaymentController::class, 'show'])->name('registration.pay.show');
    Route::post('/registration/pay/{token}', [RegistrationPaymentController::class, 'initialize'])
        ->middleware('throttle:10,1')
        ->name('registration.pay.initialize');
});

Route::get('/media/storage/{path}', [PublicStorageMediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.storage.show');

Route::get('/dashboard', function (Request $request) {
    $user = $request->user();

    if ($user && $user->canAccessAdminPanel()) {
        return redirect($user->staffHomeRoute());
    }

    if ($user && $user->isMember()) {
        return redirect()->route('portal.dashboard');
    }

    return view('dashboard', [
        'user' => $user,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/portal', [PortalController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('portal.dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::get('/dashboard/api/media', [MediaLibraryController::class, 'list'])->name('dashboard.api.media');
    Route::post('/dashboard/api/media', [MediaLibraryController::class, 'upload'])->name('dashboard.api.media.upload');
});

Route::middleware(['auth', 'staff', 'admin.audit'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        $user = request()->user();
        if ($user && ! $user->can('dashboard.view')) {
            return redirect()->route('admin.registrations.index');
        }

        $analyticsStart = now()->subDays(89)->startOfDay();
        $analyticsEnd = now();
        $analyticsBase = SiteTrafficEvent::query()->betweenDates($analyticsStart, $analyticsEnd);
        $topPage = (clone $analyticsBase)->selectRaw('path, COUNT(*) as views')->groupBy('path')->orderByDesc('views')->first();
        $topPagePath = trim((string) ($topPage->path ?? ''));
        $topPageLabel = 'No data yet';
        if ($topPagePath !== '') {
            if ($topPagePath === '/') {
                $topPageLabel = 'Homepage';
            } else {
                $clean = trim(str_replace(['-', '_', '/'], ' ', $topPagePath));
                $topPageLabel = ucwords($clean !== '' ? $clean : $topPagePath);
            }
        }

        $auditStart = now()->subDays(30)->startOfDay();
        $auditBase = AdminAuditTrail::query()->where('created_at', '>=', $auditStart);

        $totalViews = (int) (clone $analyticsBase)->count();

        return view('admin.dashboard', [
            'title' => __('Dashboard'),
            'stats' => [
                'pending_registrations' => Registration::query()->where('status', 'pending_review')->count(),
                'awaiting_payment' => Registration::query()->where('status', 'awaiting_payment')->count(),
                'active_players' => Player::query()->where('status', 'active')->count(),
                'users_count' => User::query()->count(),
                'visitors_total' => $totalViews,
            ],
            'recentRegistrations' => Registration::query()
                ->with(['program'])
                ->latest('submitted_at')
                ->take(8)
                ->get(),
            'analyticsSummary' => [
                'range_days' => 90,
                'total_views' => $totalViews,
                'unique_sessions' => (clone $analyticsBase)->whereNotNull('session_id')->distinct('session_id')->count('session_id'),
                'top_page' => $topPage,
                'top_page_label' => $topPageLabel,
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

    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->middleware('permission:analytics.view')->name('admin.analytics.index');
    Route::get('/analytics/data', [AdminAnalyticsController::class, 'data'])->middleware('permission:analytics.view')->name('admin.analytics.data');
    Route::get('/audit', function (Request $request) {
        $filters = $request->validate([
            'method' => ['nullable', 'string', 'max:10'],
            'user_id' => ['nullable', 'integer', 'min:1'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'q' => ['nullable', 'string', 'max:255'],
            'action' => ['nullable', 'string', 'max:64'],
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
                    ->orWhere('ip_address', 'like', '%'.$search.'%')
                    ->orWhere('meta->summary', 'like', '%'.$search.'%')
                    ->orWhere('meta->subject_label', 'like', '%'.$search.'%');
            });
        }

        $action = trim((string) ($filters['action'] ?? ''));
        if ($action !== '') {
            $query->where('meta->action', $action);
        }

        return view('admin.audit.index', [
            'entries' => $query->paginate(30)->withQueryString(),
            'method' => $method,
            'userId' => $userId,
            'from' => (string) ($filters['from'] ?? ''),
            'to' => (string) ($filters['to'] ?? ''),
            'search' => $search,
            'actionFilter' => $action,
            'staffActors' => User::query()
                ->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'coach']))
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ]);
    })->middleware('permission:audit.view')->name('admin.audit.index');

    Route::get('/players', [AdminPlayerController::class, 'index'])->middleware('permission:players.view')->name('admin.players.index');
    Route::get('/players/create', [AdminPlayerController::class, 'create'])->middleware('permission:players.create')->name('admin.players.create');
    Route::post('/players', [AdminPlayerController::class, 'store'])->middleware('permission:players.create')->name('admin.players.store');
    Route::get('/players/{player}', [AdminPlayerController::class, 'show'])->middleware('permission:players.view')->name('admin.players.show');
    Route::get('/players/{player}/edit', [AdminPlayerController::class, 'edit'])->middleware('permission:players.update')->name('admin.players.edit');
    Route::put('/players/{player}', [AdminPlayerController::class, 'update'])->middleware('permission:players.update')->name('admin.players.update');
    Route::delete('/players/{player}', [AdminPlayerController::class, 'destroy'])->middleware('permission:players.delete')->name('admin.players.destroy');

    Route::get('/programs', [AdminProgramController::class, 'index'])->middleware('permission:programs.view')->name('admin.programs.index');
    Route::get('/programs/create', [AdminProgramController::class, 'create'])->middleware('permission:programs.manage')->name('admin.programs.create');
    Route::post('/programs', [AdminProgramController::class, 'store'])->middleware('permission:programs.manage')->name('admin.programs.store');
    Route::get('/programs/{program}', [AdminProgramController::class, 'show'])->middleware('permission:programs.view')->name('admin.programs.show');
    Route::get('/programs/{program}/edit', [AdminProgramController::class, 'edit'])->middleware('permission:programs.manage')->name('admin.programs.edit');
    Route::put('/programs/{program}', [AdminProgramController::class, 'update'])->middleware('permission:programs.manage')->name('admin.programs.update');
    Route::delete('/programs/{program}', [AdminProgramController::class, 'destroy'])->middleware('permission:programs.manage')->name('admin.programs.destroy');

    Route::get('/coaches', [AdminCoachController::class, 'index'])->middleware('permission:coaches.view')->name('admin.coaches.index');
    Route::get('/coaches/create', [AdminCoachController::class, 'create'])->middleware('permission:coaches.manage')->name('admin.coaches.create');
    Route::post('/coaches', [AdminCoachController::class, 'store'])->middleware('permission:coaches.manage')->name('admin.coaches.store');
    Route::get('/coaches/{coach}', [AdminCoachController::class, 'show'])->middleware('permission:coaches.view')->name('admin.coaches.show');
    Route::get('/coaches/{coach}/edit', [AdminCoachController::class, 'edit'])->middleware('permission:coaches.manage')->name('admin.coaches.edit');
    Route::put('/coaches/{coach}', [AdminCoachController::class, 'update'])->middleware('permission:coaches.manage')->name('admin.coaches.update');
    Route::delete('/coaches/{coach}', [AdminCoachController::class, 'destroy'])->middleware('permission:coaches.manage')->name('admin.coaches.destroy');

    Route::get('/payments', [AdminPaymentController::class, 'index'])->middleware('permission:payments.view')->name('admin.payments.index');
    Route::get('/payments/registration/{payment}', [AdminPaymentController::class, 'showRegistration'])->middleware('permission:payments.view')->name('admin.payments.registration.show');
    Route::get('/payments/academy/{payment}', [AdminPaymentController::class, 'showAcademy'])->middleware('permission:payments.view')->name('admin.payments.academy.show');

    Route::get('/training-sessions', [AdminTrainingSessionController::class, 'index'])->middleware('permission:training_sessions.view')->name('admin.training-sessions.index');
    Route::get('/training-sessions/create', [AdminTrainingSessionController::class, 'create'])->middleware('permission:training_sessions.manage')->name('admin.training-sessions.create');
    Route::post('/training-sessions', [AdminTrainingSessionController::class, 'store'])->middleware('permission:training_sessions.manage')->name('admin.training-sessions.store');
    Route::get('/training-sessions/{trainingSession}', [AdminTrainingSessionController::class, 'show'])->middleware('permission:training_sessions.view')->name('admin.training-sessions.show');
    Route::get('/training-sessions/{trainingSession}/edit', [AdminTrainingSessionController::class, 'edit'])->middleware('permission:training_sessions.manage')->name('admin.training-sessions.edit');
    Route::put('/training-sessions/{trainingSession}', [AdminTrainingSessionController::class, 'update'])->middleware('permission:training_sessions.manage')->name('admin.training-sessions.update');
    Route::delete('/training-sessions/{trainingSession}', [AdminTrainingSessionController::class, 'destroy'])->middleware('permission:training_sessions.manage')->name('admin.training-sessions.destroy');

    Route::get('/attendance', [AdminAttendanceController::class, 'index'])->middleware('permission:attendance.view')->name('admin.attendance.index');
    Route::get('/attendance/{trainingSession}', [AdminAttendanceController::class, 'show'])->middleware('permission:attendance.view')->name('admin.attendance.show');

    Route::get('/performance', [AdminPerformanceReportController::class, 'index'])->middleware('permission:performance.view')->name('admin.performance.index');
    Route::get('/performance/create', [AdminPerformanceReportController::class, 'create'])->middleware('permission:performance.manage')->name('admin.performance.create');
    Route::post('/performance', [AdminPerformanceReportController::class, 'store'])->middleware('permission:performance.manage')->name('admin.performance.store');
    Route::get('/performance/{performanceReport}', [AdminPerformanceReportController::class, 'show'])->middleware('permission:performance.view')->name('admin.performance.show');

    Route::get('/tournaments', [AdminTournamentController::class, 'index'])->middleware('permission:tournaments.view')->name('admin.tournaments.index');
    Route::get('/tournaments/create', [AdminTournamentController::class, 'create'])->middleware('permission:tournaments.manage')->name('admin.tournaments.create');
    Route::post('/tournaments', [AdminTournamentController::class, 'store'])->middleware('permission:tournaments.manage')->name('admin.tournaments.store');
    Route::get('/tournaments/{tournament}', [AdminTournamentController::class, 'show'])->middleware('permission:tournaments.view')->name('admin.tournaments.show');
    Route::get('/tournaments/{tournament}/edit', [AdminTournamentController::class, 'edit'])->middleware('permission:tournaments.manage')->name('admin.tournaments.edit');
    Route::put('/tournaments/{tournament}', [AdminTournamentController::class, 'update'])->middleware('permission:tournaments.manage')->name('admin.tournaments.update');
    Route::delete('/tournaments/{tournament}', [AdminTournamentController::class, 'destroy'])->middleware('permission:tournaments.manage')->name('admin.tournaments.destroy');

    Route::get('/announcements', [AdminAnnouncementController::class, 'index'])->middleware('permission:announcements.view')->name('admin.announcements.index');
    Route::get('/announcements/create', [AdminAnnouncementController::class, 'create'])->middleware('permission:announcements.manage')->name('admin.announcements.create');
    Route::post('/announcements', [AdminAnnouncementController::class, 'store'])->middleware('permission:announcements.manage')->name('admin.announcements.store');
    Route::get('/announcements/{announcement}', [AdminAnnouncementController::class, 'show'])->middleware('permission:announcements.view')->name('admin.announcements.show');
    Route::get('/announcements/{announcement}/edit', [AdminAnnouncementController::class, 'edit'])->middleware('permission:announcements.manage')->name('admin.announcements.edit');
    Route::put('/announcements/{announcement}', [AdminAnnouncementController::class, 'update'])->middleware('permission:announcements.manage')->name('admin.announcements.update');
    Route::delete('/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy'])->middleware('permission:announcements.manage')->name('admin.announcements.destroy');

    Route::get('/registrations', [AdminRegistrationController::class, 'index'])
        ->middleware('permission:registrations.view')
        ->name('admin.registrations.index');
    Route::post('/registrations/{registration}/approve', [AdminRegistrationController::class, 'approve'])
        ->middleware('permission:registrations.approve')
        ->name('admin.registrations.approve');
    Route::post('/registrations/{registration}/reject', [AdminRegistrationController::class, 'reject'])
        ->middleware('permission:registrations.reject')
        ->name('admin.registrations.reject');
    Route::post('/registrations/{registration}/regenerate-token', [AdminRegistrationController::class, 'regenerateToken'])
        ->middleware('permission:registrations.approve')
        ->name('admin.registrations.regenerate-token');

    Route::get('/staff', [AdminStaffController::class, 'index'])->middleware('permission:staff.manage')->name('admin.staff.index');
    Route::post('/staff', [AdminStaffController::class, 'store'])->middleware('permission:staff.manage')->name('admin.staff.store');
    Route::put('/staff/{user}', [AdminStaffController::class, 'update'])->middleware('permission:staff.manage')->name('admin.staff.update');
    Route::delete('/staff/{user}', [AdminStaffController::class, 'destroy'])->middleware('permission:staff.manage')->name('admin.staff.destroy');

    Route::get('/users', [AdminUserController::class, 'index'])->middleware('permission:customers.view')->name('admin.users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->middleware('permission:customers.manage')->name('admin.users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->middleware('permission:customers.manage')->name('admin.users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->middleware('permission:customers.manage')->name('admin.users.destroy');

    Route::get('/pages', [AdminPageController::class, 'index'])->middleware('permission:pages.manage')->name('admin.pages.index');
    Route::get('/pages/{slug}/edit', [AdminPageController::class, 'edit'])->middleware('permission:pages.manage')->name('admin.pages.edit');
    Route::put('/pages/{slug}', [AdminPageController::class, 'update'])->middleware('permission:pages.manage')->name('admin.pages.update');
    Route::get('/media', [AdminMediaController::class, 'index'])->middleware('permission:media.manage')->name('admin.media.index');
    Route::post('/media', [MediaLibraryController::class, 'upload'])->middleware('permission:media.manage')->name('admin.media.upload');
    Route::delete('/media/{media}', [AdminMediaController::class, 'destroy'])->middleware('permission:media.manage')->name('admin.media.destroy');
    Route::post('/media/{media}', [AdminMediaController::class, 'destroy'])->middleware('permission:media.manage')->name('admin.media.destroy.post');
    Route::post('/media/bulk-delete', [AdminMediaController::class, 'bulkDestroy'])->middleware('permission:media.manage')->name('admin.media.bulk-destroy');
    Route::get('/api/media', [MediaLibraryController::class, 'list'])->middleware('permission:media.manage')->name('admin.media.list');

    Route::get('/settings', [AdminSiteSettingsController::class, 'edit'])->middleware('permission:settings.manage')->name('admin.settings.edit');
    Route::put('/settings', [AdminSiteSettingsController::class, 'update'])->middleware('permission:settings.manage')->name('admin.settings.update');
    Route::post('/settings/mail-test', [AdminSiteSettingsController::class, 'sendTestMail'])
        ->middleware(['permission:settings.manage', 'throttle:10,1'])
        ->name('admin.settings.mail-test');
});

require __DIR__.'/auth.php';

Route::get('/register', [RegistrationWizardController::class, 'show'])->name('registration.wizard');
Route::post('/register/step', [RegistrationWizardController::class, 'storeStep'])
    ->middleware('throttle:20,1')
    ->name('registration.wizard.step');
Route::post('/register/submit', [RegistrationWizardController::class, 'submit'])
    ->middleware('throttle:10,1')
    ->name('registration.wizard.submit');
Route::get('/register/complete', [RegistrationWizardController::class, 'complete'])->name('registration.complete');
