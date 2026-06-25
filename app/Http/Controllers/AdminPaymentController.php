<?php

namespace App\Http\Controllers;

use App\Models\AcademyPayment;
use App\Models\RegistrationPayment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPaymentController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'registration');
        if (! in_array($tab, ['registration', 'academy'], true)) {
            $tab = 'registration';
        }

        $registrationPayments = RegistrationPayment::query()
            ->with(['registration', 'player'])
            ->latest()
            ->paginate(15, ['*'], 'reg_page');

        $academyPayments = AcademyPayment::query()
            ->with(['player', 'season'])
            ->latest()
            ->paginate(15, ['*'], 'acad_page');

        return view('admin.payments.index', [
            'title' => __('Payments'),
            'tab' => $tab,
            'registrationPayments' => $registrationPayments,
            'academyPayments' => $academyPayments,
        ]);
    }

    public function showRegistration(RegistrationPayment $payment): View
    {
        $payment->load(['registration', 'player', 'season']);

        return view('admin.payments.show-registration', [
            'title' => __('Registration payment'),
            'payment' => $payment,
        ]);
    }

    public function showAcademy(AcademyPayment $payment): View
    {
        $payment->load(['player', 'season']);

        return view('admin.payments.show-academy', [
            'title' => __('Academy payment'),
            'payment' => $payment,
        ]);
    }
}
