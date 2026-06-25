<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AcademyAlert;
use Illuminate\Support\Collection;

class AcademyNotificationService
{
    public function notifyUser(?User $user, string $title, string $body, ?string $actionUrl = null): void
    {
        if (! $user) {
            return;
        }

        $user->notify(new AcademyAlert($title, $body, $actionUrl));
    }

    public function notifyPermissionHolders(string $permission, string $title, string $body, ?string $actionUrl = null): void
    {
        $this->usersWithPermission($permission)->each(
            fn (User $user) => $this->notifyUser($user, $title, $body, $actionUrl)
        );
    }

    /**
     * @return Collection<int, User>
     */
    private function usersWithPermission(string $permission): Collection
    {
        return User::permission($permission)->get();
    }
}
