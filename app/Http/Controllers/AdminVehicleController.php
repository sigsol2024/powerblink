<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\Admin\AdminAuditLogger;
use App\Services\Mail\OutboundMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminVehicleController extends Controller
{
    public function approve(Request $request, Vehicle $vehicle, AdminAuditLogger $audit): RedirectResponse
    {
        $vehicle->status = 'approved';
        $vehicle->approved_at = now();
        $vehicle->approved_by = $request->user()->id;
        $vehicle->rejection_reason = null;
        $vehicle->save();

        $vehicle->loadMissing('user');

        if (! empty($vehicle->user?->email) && ! $vehicle->isStaffListing()) {
            try {
                $subject = 'Your listing was approved';
                $html = view('emails.listing-approved', [
                    'user' => $vehicle->user,
                    'vehicle' => $vehicle,
                    'publicUrl' => route('inventory.show', ['slug' => $vehicle->slug]),
                ])->render();

                app(OutboundMailService::class)->send($vehicle->user->email, $vehicle->user->name ?? 'User', $subject, $html);
            } catch (Throwable $e) {
                Log::warning('Listing approved but owner notification email failed', [
                    'vehicle_id' => $vehicle->id,
                    'exception' => $e,
                ]);
            }
        }

        $audit->logProductApproved($request, $vehicle);

        return back();
    }

    public function reject(Request $request, Vehicle $vehicle, AdminAuditLogger $audit): RedirectResponse
    {
        $data = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $vehicle->status = 'rejected';
        $vehicle->approved_at = null;
        $vehicle->approved_by = $request->user()->id;
        $vehicle->rejection_reason = $data['rejection_reason'] ?? 'Rejected';
        $vehicle->save();

        $vehicle->loadMissing('user');

        if (! empty($vehicle->user?->email) && ! $vehicle->isStaffListing()) {
            try {
                $subject = 'Your listing was rejected';
                $html = view('emails.listing-rejected', [
                    'user' => $vehicle->user,
                    'vehicle' => $vehicle,
                    'reason' => (string) $vehicle->rejection_reason,
                    'editUrl' => route('dashboard.vehicles.edit', $vehicle),
                ])->render();

                app(OutboundMailService::class)->send($vehicle->user->email, $vehicle->user->name ?? 'User', $subject, $html);
            } catch (Throwable $e) {
                Log::warning('Listing rejected but owner notification email failed', [
                    'vehicle_id' => $vehicle->id,
                    'exception' => $e,
                ]);
            }
        }

        $audit->logProductRejected($request, $vehicle);

        return back();
    }
}
