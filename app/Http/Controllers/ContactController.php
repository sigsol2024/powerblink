<?php

namespace App\Http\Controllers;

use App\Support\BrandedMailContext;
use App\Support\SiteSettingDefaults;
use App\Services\Mail\OutboundMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Throwable;

class ContactController extends Controller
{
    public function submit(Request $request, OutboundMailService $mailer): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
            'newsletter_subscribe' => ['nullable', 'boolean'],
        ]);

        $data = [
            'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'],
        ];

        $to = SiteSettingDefaults::resolvedNotifyEmail();

        $subject = 'New contact form submission';
        $html = view('emails.contact', $data)->render();

        try {
            $mailer->send(
                $to,
                SiteSettingDefaults::resolvedNotifyRecipientName(),
                $subject,
                $html,
                $data['email'],
                $data['name']
            );
        } catch (RuntimeException) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Mail is not configured or the request could not be sent. Please try again later.']);
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['email' => 'Could not send message. Please try again later.']);
        }

        if ($request->boolean('newsletter_subscribe')) {
            NewsletterController::notifyIfEnabled($mailer, $validated['email']);
        }

        $siteName = BrandedMailContext::forEmail()['siteName'];

        return back()->with(
            'status',
            __('Thank you for contacting :site. Our customer representative will contact you soon. Thank you.', [
                'site' => $siteName,
            ])
        );
    }
}

