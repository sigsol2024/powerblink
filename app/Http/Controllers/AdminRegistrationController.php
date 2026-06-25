<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $status = trim((string) $request->query('status', 'pending_review'));
        $allowed = ['pending_review', 'awaiting_payment', 'activated', 'rejected', 'all'];

        if (! in_array($status, $allowed, true)) {
            $status = 'pending_review';
        }

        $query = Registration::query()
            ->with(['guardian', 'program', 'season'])
            ->latest('submitted_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $counts = [
            'pending_review' => Registration::query()->where('status', 'pending_review')->count(),
            'awaiting_payment' => Registration::query()->where('status', 'awaiting_payment')->count(),
            'activated' => Registration::query()->where('status', 'activated')->count(),
            'rejected' => Registration::query()->where('status', 'rejected')->count(),
        ];

        return view('admin.registrations.index', [
            'title' => __('Registrations'),
            'registrations' => $query->paginate(20)->withQueryString(),
            'status' => $status,
            'counts' => $counts,
        ]);
    }

    public function approve(Request $request, Registration $registration, RegistrationService $service): RedirectResponse
    {
        $service->approve($registration, $request->user());

        return back()->with('status', __('Registration approved. Payment link emailed to guardian.'));
    }

    public function reject(Request $request, Registration $registration, RegistrationService $service): RedirectResponse
    {
        $data = $request->validate([
            'rejected_reason' => ['required', 'string', 'max:2000'],
        ]);

        $service->reject($registration, $data['rejected_reason'], $request->user());

        return back()->with('status', __('Registration rejected.'));
    }

    public function regenerateToken(Registration $registration, RegistrationService $service): RedirectResponse
    {
        $service->regeneratePaymentToken($registration);

        return back()->with('status', __('New payment link generated and emailed.'));
    }
}
