<?php

namespace App\Http\Controllers;

use App\Models\AcademyPayment;
use App\Models\InstallmentPlan;
use App\Models\PerformanceReport;
use App\Models\Player;
use App\Models\Registration;
use App\Models\RegistrationPayment;
use App\Models\SessionAttendance;
use App\Models\Tournament;
use App\Models\TrainingSession;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = request()->user();
        if ($user && ! $user->can('dashboard.view')) {
            return redirect()->route('admin.registrations.index');
        }

        $monthStart = now()->startOfMonth();

        $monthlyRevenue = (int) RegistrationPayment::query()
            ->where('status', 'paid')
            ->where('paid_at', '>=', $monthStart)
            ->sum('amount')
            + (int) AcademyPayment::query()
                ->where('status', 'paid')
                ->where('paid_at', '>=', $monthStart)
                ->sum('amount');

        $attendanceStart = now()->subDays(30)->toDateString();
        $attendanceTotal = SessionAttendance::query()
            ->whereHas('trainingSession', fn ($q) => $q->where('date', '>=', $attendanceStart))
            ->count();
        $attendancePresent = SessionAttendance::query()
            ->whereHas('trainingSession', fn ($q) => $q->where('date', '>=', $attendanceStart))
            ->where('status', 'present')
            ->count();
        $attendanceRate = $attendanceTotal > 0
            ? (int) round(($attendancePresent / $attendanceTotal) * 100)
            : null;

        $activeRegistrations = Registration::query()
            ->whereIn('status', ['pending_review', 'awaiting_payment', 'approved', 'active'])
            ->count();

        $upcomingEvents = $this->upcomingEvents();

        $overdueCount = InstallmentPlan::query()
            ->where('status', 'pending')
            ->where('due_date', '<', today())
            ->count();
        $dueThisWeekCount = InstallmentPlan::query()
            ->where('status', 'pending')
            ->whereBetween('due_date', [today(), today()->addDays(7)])
            ->count();
        $pendingPaymentCount = RegistrationPayment::query()->where('status', 'pending')->count()
            + AcademyPayment::query()->where('status', 'pending')->count();

        $overdueAmount = (int) InstallmentPlan::query()
            ->where('status', 'pending')
            ->where('due_date', '<', today())
            ->sum('amount');
        $dueWeekAmount = (int) InstallmentPlan::query()
            ->where('status', 'pending')
            ->whereBetween('due_date', [today(), today()->addDays(7)])
            ->sum('amount');
        $pendingAmountTotal = max(1, $overdueAmount + $dueWeekAmount);

        $performanceTrends = $this->performanceTrends();

        return view('admin.dashboard', [
            'title' => __('Dashboard'),
            'stats' => [
                'active_players' => Player::query()->where('status', 'active')->count(),
                'active_registrations' => $activeRegistrations,
                'monthly_revenue' => $monthlyRevenue,
                'attendance_rate' => $attendanceRate,
            ],
            'recentRegistrations' => Registration::query()
                ->with(['program'])
                ->latest('submitted_at')
                ->take(8)
                ->get(),
            'upcomingEvents' => $upcomingEvents,
            'pendingPayments' => [
                'total_count' => $pendingPaymentCount,
                'overdue_count' => $overdueCount,
                'due_week_count' => $dueThisWeekCount,
                'overdue_amount' => $overdueAmount,
                'due_week_amount' => $dueWeekAmount,
                'overdue_pct' => (int) round(($overdueAmount / $pendingAmountTotal) * 100),
                'due_week_pct' => (int) round(($dueWeekAmount / $pendingAmountTotal) * 100),
            ],
            'performanceTrends' => $performanceTrends,
        ]);
    }

    /**
     * @return array<int, array{type:string,title:string,date:Carbon,location:?string,category:?string}>
     */
    protected function upcomingEvents(): array
    {
        $events = [];

        $sessions = TrainingSession::query()
            ->where('date', '>=', today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->with(['program'])
            ->take(3)
            ->get();

        foreach ($sessions as $session) {
            $events[] = [
                'type' => 'session',
                'title' => $session->title,
                'date' => Carbon::parse($session->date),
                'location' => $session->location,
                'category' => $session->program?->name ?? $session->session_type,
            ];
        }

        if (count($events) < 3) {
            $tournaments = Tournament::query()
                ->where('start_date', '>=', today())
                ->orderBy('start_date')
                ->take(3 - count($events))
                ->get();

            foreach ($tournaments as $tournament) {
                $events[] = [
                    'type' => 'tournament',
                    'title' => $tournament->title,
                    'date' => Carbon::parse($tournament->start_date),
                    'location' => $tournament->location,
                    'category' => $tournament->category ?? __('Tournament'),
                ];
            }
        }

        return $events;
    }

    /**
     * @return array{months: array<int, array{label:string,value:float,height:int}>, max: float}
     */
    protected function performanceTrends(): array
    {
        $months = [];
        $max = 1.0;

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $avg = PerformanceReport::query()
                ->whereBetween('reported_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->avg('overall_score');
            $value = $avg !== null ? round((float) $avg, 1) : 0.0;
            $max = max($max, $value);
            $months[] = [
                'label' => $month->format('M'),
                'value' => $value,
                'height' => 0,
            ];
        }

        foreach ($months as &$row) {
            $row['height'] = $row['value'] > 0 ? (int) round(($row['value'] / $max) * 100) : 8;
        }
        unset($row);

        return ['months' => $months, 'max' => $max];
    }
}
