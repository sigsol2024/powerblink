<?php

namespace App\Services\Admin;

use App\Models\AdminAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuditLogger
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function log(Request $request, string $action, string $summary, array $meta = []): void
    {
        $user = Auth::user();

        if (! $user || ! $user->isStaff()) {
            return;
        }

        try {
            AdminAuditTrail::query()->create([
                'user_id' => $user->id,
                'method' => strtoupper($request->method()),
                'path' => '/'.ltrim((string) $request->path(), '/'),
                'route_name' => optional($request->route())->getName(),
                'status_code' => 200,
                'ip_address' => (string) $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1000),
                'meta' => array_merge([
                    'action' => $action,
                    'summary' => $summary,
                ], $meta),
            ]);
        } catch (\Throwable) {
            // Fail open: audit logging should never block admin actions.
        }
    }

    public function logStaffCreated(Request $request, \App\Models\User $target, string $roleName): void
    {
        $this->log($request, 'staff.created', __('Created :role :name', [
            'role' => $roleName,
            'name' => $target->name,
        ]), [
            'subject_type' => 'user',
            'subject_id' => $target->id,
            'subject_label' => $target->name.' ('.$target->email.')',
            'role' => $roleName,
        ]);
    }

    /**
     * @param  list<string>  $changedFields
     */
    public function logStaffUpdated(Request $request, \App\Models\User $target, array $changedFields, bool $passwordChanged): void
    {
        $this->log($request, 'staff.updated', __('Updated staff account :name', ['name' => $target->name]), [
            'subject_type' => 'user',
            'subject_id' => $target->id,
            'subject_label' => $target->name.' ('.$target->email.')',
            'changed_fields' => $changedFields,
            'password_changed' => $passwordChanged,
        ]);
    }

    public function logStaffDeleted(Request $request, \App\Models\User $target): void
    {
        $this->log($request, 'staff.deleted', __('Deleted staff account :name', ['name' => $target->name]), [
            'subject_type' => 'user',
            'subject_id' => $target->id,
            'subject_label' => $target->name.' ('.$target->email.')',
        ]);
    }

    public function logCustomerCreated(Request $request, \App\Models\User $target): void
    {
        $this->log($request, 'customer.created', __('Created customer :name', ['name' => $target->name]), [
            'subject_type' => 'user',
            'subject_id' => $target->id,
            'subject_label' => $target->name.' ('.$target->email.')',
        ]);
    }

    /**
     * @param  list<string>  $changedFields
     */
    public function logCustomerUpdated(Request $request, \App\Models\User $target, array $changedFields, bool $passwordChanged): void
    {
        $this->log($request, 'customer.updated', __('Updated customer :name', ['name' => $target->name]), [
            'subject_type' => 'user',
            'subject_id' => $target->id,
            'subject_label' => $target->name.' ('.$target->email.')',
            'changed_fields' => $changedFields,
            'password_changed' => $passwordChanged,
        ]);
    }

    public function logCustomerDeleted(Request $request, \App\Models\User $target): void
    {
        $this->log($request, 'customer.deleted', __('Deleted customer :name', ['name' => $target->name]), [
            'subject_type' => 'user',
            'subject_id' => $target->id,
            'subject_label' => $target->name.' ('.$target->email.')',
        ]);
    }

    public function logProductCreated(Request $request, \App\Models\Vehicle $vehicle): void
    {
        $this->log($request, 'product.created', __('Created product :title', ['title' => $vehicle->title]), [
            'subject_type' => 'product',
            'subject_id' => $vehicle->id,
            'subject_label' => $vehicle->title,
        ]);
    }

    public function logProductUpdated(Request $request, \App\Models\Vehicle $vehicle): void
    {
        $this->log($request, 'product.updated', __('Updated product :title', ['title' => $vehicle->title]), [
            'subject_type' => 'product',
            'subject_id' => $vehicle->id,
            'subject_label' => $vehicle->title,
        ]);
    }

    public function logProductDeleted(Request $request, \App\Models\Vehicle $vehicle): void
    {
        $this->log($request, 'product.deleted', __('Deleted product :title', ['title' => $vehicle->title]), [
            'subject_type' => 'product',
            'subject_id' => $vehicle->id,
            'subject_label' => $vehicle->title,
        ]);
    }

    public function logProductApproved(Request $request, \App\Models\Vehicle $vehicle): void
    {
        $this->log($request, 'product.approved', __('Approved product :title', ['title' => $vehicle->title]), [
            'subject_type' => 'product',
            'subject_id' => $vehicle->id,
            'subject_label' => $vehicle->title,
        ]);
    }

    public function logProductRejected(Request $request, \App\Models\Vehicle $vehicle): void
    {
        $this->log($request, 'product.rejected', __('Rejected product :title', ['title' => $vehicle->title]), [
            'subject_type' => 'product',
            'subject_id' => $vehicle->id,
            'subject_label' => $vehicle->title,
        ]);
    }
}
