<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Services\RegistrationDocumentService;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationWizardController extends Controller
{
    private const SESSION_KEY = 'registration_wizard';

    private const STEPS = [
        1 => 'Player Information',
        2 => 'Guardian Details',
        3 => 'Medical & Fitness',
        4 => 'Documents',
        5 => 'Program & Payment Plan',
        6 => 'Review & Submit',
    ];

    public function show(Request $request, RegistrationService $registrations): View
    {
        $step = max(1, min(6, (int) $request->query('step', 1)));
        $season = $registrations->resolveActiveSeason();
        $programs = $season
            ? Program::query()->where('season_id', $season->id)->where('is_active', true)->orderBy('sort_order')->get()
            : collect();

        return view('registrations.wizard', [
            'title' => __('Player Registration'),
            'step' => $step,
            'steps' => self::STEPS,
            'wizard' => $request->session()->get(self::SESSION_KEY, []),
            'season' => $season,
            'programs' => $programs,
        ]);
    }

    public function storeStep(Request $request, RegistrationDocumentService $documents): RedirectResponse
    {
        $step = max(1, min(5, (int) $request->input('step', 1)));

        if ($step === 4) {
            $request->validate([
                'profile_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'birth_certificate' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
                'medical_clearance' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            ]);

            $wizard = $request->session()->get(self::SESSION_KEY, []);
            $wizard['document_media_ids'] = array_merge(
                (array) ($wizard['document_media_ids'] ?? []),
                $documents->storeWizardUploads([
                    'profile_photo' => $request->file('profile_photo'),
                    'birth_certificate' => $request->file('birth_certificate'),
                    'medical_clearance' => $request->file('medical_clearance'),
                ]),
            );
            $request->session()->put(self::SESSION_KEY, $wizard);

            return redirect()->route('registration.wizard', ['step' => 5]);
        }

        $rules = $this->rulesForStep($step);
        $validated = $request->validate($rules);

        $wizard = $request->session()->get(self::SESSION_KEY, []);
        $wizard = array_merge($wizard, $validated);
        $request->session()->put(self::SESSION_KEY, $wizard);

        return redirect()->route('registration.wizard', ['step' => $step + 1]);
    }

    public function submit(Request $request, RegistrationService $registrations, RegistrationDocumentService $documents): RedirectResponse
    {
        $wizard = $request->session()->get(self::SESSION_KEY, []);
        $request->validate(array_merge(
            $this->rulesForStep(1),
            $this->rulesForStep(2),
            $this->rulesForStep(3),
            $this->rulesForStep(5),
            ['terms_accepted' => ['accepted']],
        ));

        $registration = $registrations->submit($wizard);
        $documents->attachToRegistration($registration, (array) ($wizard['document_media_ids'] ?? []));
        $request->session()->forget(self::SESSION_KEY);
        $request->session()->put('registration_complete_reference', $registration->reference_code);

        return redirect()->route('registration.complete');
    }

    public function complete(Request $request): View|RedirectResponse
    {
        $reference = $request->session()->pull('registration_complete_reference');
        if (! is_string($reference) || $reference === '') {
            return redirect()->route('registration.wizard');
        }

        return view('registrations.complete', [
            'title' => __('Application received'),
            'referenceCode' => $reference,
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'player_name' => ['required', 'string', 'max:255'],
                'date_of_birth' => ['required', 'date', 'before:today'],
                'nationality' => ['nullable', 'string', 'max:120'],
                'primary_position' => ['nullable', 'string', 'max:80'],
                'secondary_position' => ['nullable', 'string', 'max:80'],
                'years_experience' => ['nullable', 'integer', 'min:0', 'max:30'],
                'technical_strengths' => ['nullable', 'string', 'max:2000'],
            ],
            2 => [
                'guardian_name' => ['required', 'string', 'max:255'],
                'guardian_relationship' => ['nullable', 'string', 'max:80'],
                'guardian_phone' => ['nullable', 'string', 'max:40'],
                'guardian_email' => ['required', 'email', 'max:255'],
                'guardian_address' => ['nullable', 'string', 'max:2000'],
                'emergency_contact_name' => ['nullable', 'string', 'max:255'],
                'emergency_contact_phone' => ['nullable', 'string', 'max:40'],
                'emergency_contact_relationship' => ['nullable', 'string', 'max:80'],
            ],
            3 => [
                'allergies' => ['nullable', 'string', 'max:2000'],
                'medical_history' => ['nullable', 'string', 'max:5000'],
                'fitness_certified' => ['sometimes', 'boolean'],
            ],
            5 => [
                'program_id' => ['required', 'integer', 'exists:programs,id'],
                'payment_plan' => ['required', 'in:lump_sum,installments'],
            ],
            default => [],
        };
    }
}
