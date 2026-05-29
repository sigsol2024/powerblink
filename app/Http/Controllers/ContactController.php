<?php

namespace App\Http\Controllers;

use App\Services\Mail\OutboundMailService;
use App\Support\SiteSettingDefaults;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Throwable;

class ContactController extends Controller
{
    public function submit(Request $request, OutboundMailService $mailer): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required_without_all:first_name,last_name', 'nullable', 'string', 'max:255'],
            'first_name' => ['required_without:name', 'nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'newsletter_subscribe' => ['nullable', 'boolean'],
        ]);

        if (! empty($validated['name'])) {
            $parts = preg_split('/\s+/', trim($validated['name']), 2);
            $firstName = $parts[0] ?? '';
            $lastName = $parts[1] ?? '';
        } else {
            $firstName = $validated['first_name'];
            $lastName = $validated['last_name'] ?? '';
        }

        $message = $validated['message'];
        if (! empty($validated['subject'])) {
            $message = 'Subject: '.$validated['subject']."\n\n".$message;
        }

        $data = [
            'name' => trim($firstName.' '.$lastName),
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $message,
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
            return $this->submitFailed(
                $request,
                __('Mail is not configured or the request could not be sent. Please try again later.')
            );
        } catch (Throwable $e) {
            report($e);

            return $this->submitFailed(
                $request,
                __('Could not send message. Please try again later.')
            );
        }

        if ($request->boolean('newsletter_subscribe')) {
            NewsletterController::notifyIfEnabled($mailer, $validated['email']);
        }

        $statusMessage = __('Thank you for contacting us. Our sales team will get back to you shortly.');
        $detailMessage = __('We appreciate you taking the time to write to us. A member of our sales team will review your inquiry and respond as soon as possible.');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $statusMessage,
                'detail' => $detailMessage,
                'title' => __('Message sent'),
                'sendAnother' => __('Send another message'),
            ]);
        }

        return back()->with('status', $statusMessage);
    }

    protected function submitFailed(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'errors' => ['email' => [$message]],
            ], 422);
        }

        return back()
            ->withInput()
            ->withErrors(['email' => $message]);
    }
}
