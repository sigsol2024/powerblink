<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function manageStaff(User $actor): bool
    {
        return $actor->can('staff.manage');
    }

    public function updateStaff(User $actor, User $target): bool
    {
        if (! $actor->can('staff.manage')) {
            return false;
        }

        return $target->hasRole('admin');
    }

    public function deleteStaff(User $actor, User $target): bool
    {
        if (! $actor->can('staff.manage')) {
            return false;
        }

        if ($actor->id === $target->id) {
            return false;
        }

        if ($target->isSuperAdmin()) {
            return false;
        }

        return $target->hasRole('admin');
    }

    public function manageCustomers(User $actor): bool
    {
        return $actor->can('customers.manage');
    }

    public function updateCustomer(User $actor, User $target): bool
    {
        return $actor->can('customers.manage') && $target->hasRole('user');
    }

    public function deleteCustomer(User $actor, User $target): bool
    {
        return $actor->can('customers.manage') && $target->hasRole('user') && $actor->id !== $target->id;
    }
}
