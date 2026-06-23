<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithVehicleForms;
use App\Models\ListingOption;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Services\Admin\AdminAuditLogger;
use App\Services\Mail\OutboundMailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class UserVehicleController extends Controller
{
    use InteractsWithVehicleForms;

    public function index(Request $request): View
    {
        $isStaffList = $request->user()->can('products.manage') && $request->user()->isStaff();

        $query = Vehicle::query()
            ->with(['user.roles', 'categoryOption'])
            ->latest();

        if (! $isStaffList) {
            $query->where('user_id', $request->user()->id);
        } else {
            $status = $request->query('status');
            if (is_string($status) && in_array($status, ['pending', 'approved', 'draft', 'rejected'], true)) {
                $query->where('status', $status);
            }
        }

        $vehicles = $query->paginate(15)->withQueryString();

        return view('dashboard.vehicles.index', [
            'vehicles' => $vehicles,
            'isAdminList' => $isStaffList,
            'statusFilter' => $isStaffList ? (string) $request->query('status', '') : '',
            'stats' => $isStaffList ? [
                'total' => Vehicle::query()->count(),
                'pending' => Vehicle::query()->where('status', 'pending')->count(),
                'approved' => Vehicle::query()->where('status', 'approved')->count(),
            ] : null,
        ]);
    }

    public function create(): View
    {
        return view('dashboard.vehicles.create', [
            'productCategories' => $this->productCategoryOptions(),
            ...$this->variantFormContext(),
        ]);
    }

    public function store(Request $request, AdminAuditLogger $audit): RedirectResponse
    {
        try {
            $data = $this->validateVehicleData($request);

            $vehicle = DB::transaction(function () use ($request, $data) {
                $vehicle = Vehicle::query()->create([
                    ...$data,
                    'user_id' => $request->user()->id,
                    'slug' => $this->uniqueSlug($data['title']),
                    'status' => 'draft',
                ]);

                $this->storeUploadedImages($request, $vehicle);
                $this->syncProductVariants($request, $vehicle);

                return $vehicle;
            });

            if ($request->user()->isStaff() && $request->boolean('approve_listing')) {
                $vehicle->refresh();
                $vehicle->status = 'approved';
                $vehicle->approved_at = now();
                $vehicle->approved_by = $request->user()->id;
                $vehicle->rejection_reason = null;
                $vehicle->save();
                $this->notifyOwnerListingApproved($vehicle);
            }

            if ($request->user()->isStaff()) {
                $audit->logProductCreated($request, $vehicle->fresh());
            }

            return redirect()->route('dashboard.vehicles.edit', $vehicle);
        } catch (QueryException $exception) {
            if (str_contains(strtolower($exception->getMessage()), 'is_special')) {
                return back()
                    ->withInput()
                    ->withErrors(['is_special' => __('Listing schema is out of date. Run migrations and try again.')]);
            }
            throw $exception;
        }
    }

    public function edit(Request $request, Vehicle $vehicle): View
    {
        $this->authorizeVehicleAccess($request, $vehicle);

        $vehicle->load(['images', 'user']);

        return view('dashboard.vehicles.edit', [
            'vehicle' => $vehicle,
            'isAdminEdit' => $request->user()->isStaff(),
            'productCategories' => $this->productCategoryOptions(),
            ...$this->variantFormContext($vehicle),
        ]);
    }

    /**
     * Active product-category options for the create/edit form dropdown. Returns an
     * empty collection while the listing_options.product_category category is unseeded.
     */
    private function productCategoryOptions(): \Illuminate\Support\Collection
    {
        return ListingOption::query()
            ->whereHas('category', fn ($q) => $q->where('slug', 'product_category'))
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('value')
            ->get(['id', 'value']);
    }

    public function update(Request $request, Vehicle $vehicle, AdminAuditLogger $audit): RedirectResponse
    {
        $this->authorizeVehicleAccess($request, $vehicle);
        try {
            $data = $this->validateVehicleData($request);
            $removeImageIds = collect($data['remove_image_ids'] ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->values()
                ->all();
            unset($data['remove_image_ids']);

            DB::transaction(function () use ($request, $vehicle, $data, $removeImageIds) {
                $vehicle->fill($data);
                if ($vehicle->isDirty('title')) {
                    $vehicle->slug = $this->uniqueSlug($data['title'], $vehicle->id);
                }
                $vehicle->save();
                if ($removeImageIds !== []) {
                    $vehicle->images()->whereIn('id', $removeImageIds)->delete();
                    $this->resequenceImages($vehicle);
                }
                $this->storeUploadedImages($request, $vehicle);
                $this->syncProductVariants($request, $vehicle);
            });

            if (
                $request->user()->isStaff()
                && $request->boolean('approve_listing')
                && in_array($vehicle->status, ['pending', 'draft', 'rejected'], true)
            ) {
                $vehicle->status = 'approved';
                $vehicle->approved_at = now();
                $vehicle->approved_by = $request->user()->id;
                $vehicle->rejection_reason = null;
                $vehicle->save();
                $this->notifyOwnerListingApproved($vehicle);
            }

            if ($request->user()->isStaff()) {
                $audit->logProductUpdated($request, $vehicle->fresh());
            }

            return back()->with('status', 'Listing updated.');
        } catch (QueryException $exception) {
            if (str_contains(strtolower($exception->getMessage()), 'is_special')) {
                return back()
                    ->withInput()
                    ->withErrors(['is_special' => __('Listing schema is out of date. Run migrations and try again.')]);
            }
            throw $exception;
        }
    }

    public function submit(Request $request, Vehicle $vehicle): RedirectResponse
    {
        abort_unless($vehicle->user_id === $request->user()->id, 403);

        if (! in_array($vehicle->status, ['draft', 'rejected'], true)) {
            return back();
        }

        $vehicle->status = 'pending';
        $vehicle->submitted_at = now();
        $vehicle->rejection_reason = null;
        $vehicle->save();

        $to = (string) config('mail.outbound.admin_to');
        if ($to !== '') {
            try {
                $subject = 'Listing submitted for approval';
                $html = view('emails.listing-submitted', [
                    'user' => $request->user(),
                    'vehicle' => $vehicle,
                    'adminUrl' => route('dashboard.vehicles.index'),
                ])->render();

                app(OutboundMailService::class)->send($to, 'Admin', $subject, $html);
            } catch (Throwable $e) {
                Log::warning('Listing submitted but admin notification email failed', [
                    'vehicle_id' => $vehicle->id,
                    'exception' => $e,
                ]);
            }
        }

        return back();
    }

    public function destroy(Request $request, Vehicle $vehicle, AdminAuditLogger $audit): RedirectResponse
    {
        $this->authorizeVehicleAccess($request, $vehicle);

        if ($request->user()->isStaff()) {
            $audit->logProductDeleted($request, $vehicle);
        }

        $this->deleteLocalVehicleImageFiles($vehicle);
        $vehicle->delete();

        return redirect()
            ->route('dashboard.vehicles.index')
            ->with('status', 'Listing deleted.');
    }

    public function destroyImage(Request $request, Vehicle $vehicle, VehicleImage $image): RedirectResponse|JsonResponse
    {
        $this->authorizeVehicleAccess($request, $vehicle);
        abort_unless($image->vehicle_id === $vehicle->id, 404);

        // Editor "remove" detaches image from this listing only.
        // It must not delete the underlying media asset/site file.
        $image->delete();
        $this->resequenceImages($vehicle);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Image removed.',
            ]);
        }

        return back()->with('status', 'Image removed.');
    }

    private function authorizeVehicleAccess(Request $request, Vehicle $vehicle): void
    {
        if ($request->user()->can('products.manage') && $request->user()->isStaff()) {
            return;
        }

        abort_unless($vehicle->user_id === $request->user()->id, 403);
    }

    private function notifyOwnerListingApproved(Vehicle $vehicle): void
    {
        $vehicle->loadMissing('user');
        if (empty($vehicle->user?->email) || $vehicle->isStaffListing()) {
            return;
        }

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
}
