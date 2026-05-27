<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleInquiry;
use App\Services\Mail\OutboundMailService;
use App\Support\SiteBrand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class VehicleInquiryController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse
    {
        $vehicle = Vehicle::query()
            ->with('user')
            ->where('slug', $slug)
            ->firstOrFail();

        if ($vehicle->status !== 'approved') {
            abort(404);
        }

        $data = $request->validate([
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_email' => ['required', 'email', 'max:255'],
            'sender_phone' => ['nullable', 'string', 'max:64'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        VehicleInquiry::query()->create([
            'vehicle_id' => $vehicle->id,
            'user_id' => Auth::id(),
            'sender_name' => $data['sender_name'],
            'sender_email' => $data['sender_email'],
            'message' => $data['message'],
        ]);

        $adminRaw = (string) config('mail.outbound.admin_to', '');
        $adminEmails = collect(preg_split('/\s*[,;]\s*/', $adminRaw, -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn (string $e) => trim($e))
            ->filter()
            ->unique()
            ->values();

        if ($adminEmails->isEmpty()) {
            $fallback = (string) config('mail.from.address', '');
            if ($fallback !== '') {
                $adminEmails = collect([$fallback]);
            }
        }

        $seller = $vehicle->user;
        $subject = 'New inquiry: '.$vehicle->title;
        $html = view('emails.vehicle-inquiry', [
            'vehicle' => $vehicle,
            'listingOwner' => $seller,
            'senderName' => $data['sender_name'],
            'senderEmail' => $data['sender_email'],
            'senderPhone' => trim((string) ($data['sender_phone'] ?? '')),
            'body' => $data['message'],
            'listingUrl' => route('inventory.show', ['slug' => $vehicle->slug]),
        ])->render();

        $toName = (string) config('mail.from.name', SiteBrand::displayName());
        foreach ($adminEmails as $adminEmail) {
            try {
                app(OutboundMailService::class)->send(
                    $adminEmail,
                    $toName,
                    $subject,
                    $html,
                    $data['sender_email'],
                    $data['sender_name']
                );
            } catch (Throwable $e) {
                Log::warning('Vehicle inquiry email to admin failed', [
                    'vehicle_id' => $vehicle->id,
                    'admin_email' => $adminEmail,
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        return back()->with('status', __('Your message was sent to our team.'));
    }
}
