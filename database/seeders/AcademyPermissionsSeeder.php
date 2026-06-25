<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AcademyPermissionsSeeder extends Seeder
{
    /** @var list<string> */
    public const LEGACY_PERMISSIONS = [
        'products.manage',
        'categories.manage',
        'variants.manage',
        'orders.manage',
        'customers.view',
        'customers.manage',
    ];

    /** @var list<string> */
    public const ALL = [
        'dashboard.view',
        'analytics.view',
        'players.view',
        'players.create',
        'players.update',
        'players.delete',
        'registrations.view',
        'registrations.create',
        'registrations.update',
        'registrations.approve',
        'registrations.reject',
        'registrations.delete',
        'programs.view',
        'programs.manage',
        'seasons.view',
        'seasons.manage',
        'training_sessions.view',
        'training_sessions.manage',
        'attendance.view',
        'attendance.manage',
        'performance.view',
        'performance.manage',
        'payments.view',
        'payments.manage',
        'payments.pay',
        'coaches.view',
        'coaches.manage',
        'tournaments.view',
        'tournaments.manage',
        'tournaments.squads',
        'announcements.view',
        'announcements.manage',
        'communications.receive',
        'pages.manage',
        'gallery.manage',
        'media.manage',
        'media.upload',
        'documents.view',
        'documents.upload',
        'documents.verify',
        'documents.manage',
        'settings.manage',
        'staff.manage',
        'audit.view',
    ];

    /** @var list<string> */
    public const COACH = [
        'dashboard.view',
        'analytics.view',
        'players.view',
        'registrations.view',
        'programs.view',
        'seasons.view',
        'training_sessions.view',
        'training_sessions.manage',
        'attendance.view',
        'attendance.manage',
        'performance.view',
        'performance.manage',
        'coaches.view',
        'tournaments.view',
        'tournaments.squads',
        'announcements.view',
        'communications.receive',
        'documents.view',
        'media.upload',
    ];

    /** @var list<string> */
    public const PARENT = [
        'dashboard.view',
        'players.view',
        'registrations.view',
        'programs.view',
        'seasons.view',
        'training_sessions.view',
        'attendance.view',
        'performance.view',
        'payments.view',
        'payments.pay',
        'coaches.view',
        'tournaments.view',
        'announcements.view',
        'communications.receive',
        'documents.view',
        'documents.upload',
    ];

    /** @var list<string> */
    public const PLAYER = [
        'dashboard.view',
        'players.view',
        'programs.view',
        'seasons.view',
        'training_sessions.view',
        'attendance.view',
        'performance.view',
        'payments.view',
        'coaches.view',
        'tournaments.view',
        'announcements.view',
        'communications.receive',
        'documents.view',
    ];

    /** @var list<string> */
    public const LEGACY_ROLES = [
        'editor',
        'user',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::LEGACY_PERMISSIONS as $name) {
            Permission::query()->where('name', $name)->delete();
        }

        foreach (self::ALL as $name) {
            Permission::findOrCreate($name, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', self::ALL)
            ->get();

        foreach (self::LEGACY_ROLES as $roleName) {
            $role = Role::query()->where('name', $roleName)->first();
            if ($role !== null) {
                $role->permissions()->detach();
                $role->delete();
            }
        }

        $admin = Role::findOrCreate('admin', 'web');
        $coach = Role::findOrCreate('coach', 'web');
        $parent = Role::findOrCreate('parent', 'web');
        $player = Role::findOrCreate('player', 'web');

        $admin->syncPermissions($permissions);
        $coach->syncPermissions($permissions->whereIn('name', self::COACH)->values());
        $parent->syncPermissions($permissions->whereIn('name', self::PARENT)->values());
        $player->syncPermissions($permissions->whereIn('name', self::PLAYER)->values());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
