# PowerBlink FC — Role & Permission Matrix

**Document:** `02-role-permissions.md`  
**Version:** 1.0  
**Status:** Phase 0 specification (pre-implementation)  
**Last updated:** 2026-06-24

This document defines the Spatie Laravel Permission roles and granular permissions for PowerBlink FC. It replaces the current ecommerce permission set (`products.manage`, `orders.manage`, etc.) defined in `database/seeders/PermissionsSeeder.php`.

---

## Table of contents

1. [Overview](#overview)
2. [Roles](#roles)
3. [Permission catalog](#permission-catalog)
4. [Role-to-permission assignments](#role-to-permission-assignments)
5. [Module access matrix](#module-access-matrix)
6. [Authorization implementation notes](#authorization-implementation-notes)
7. [Migration from ecommerce roles](#migration-from-ecommerce-roles)

---

## Overview

PowerBlink FC uses [Spatie Laravel Permission](https://github.com/spatie/laravel-permission) with the `web` guard. Authorization is enforced at three layers:

1. **Route middleware** — `permission:` and `role:` middleware on admin routes
2. **Policy classes** — Row-level checks (e.g. parent sees only their children)
3. **Navigation config** — Admin and member portal nav items filtered by `can()` checks

Super admin access combines the `admin` Spatie role with `users.is_super_admin = true` for destructive operations (staff management, site settings).

---

## Roles

| Role | Spatie name | Maps from existing | Description |
|------|-------------|-------------------|-------------|
| **Super Admin** | `admin` + `is_super_admin` | `admin` user flag | Full system access including staff, audit, and site settings |
| **Admin** | `admin` | `admin` (without super flag) | Full academy operations; may exclude staff CRUD depending on policy |
| **Coach** | `coach` | `editor` (repurposed) | Squad management, attendance, performance reports; no payments or settings |
| **Parent** | `parent` | *new* | View and pay for own children's data; upload documents |
| **Player** | `player` | *new* | View own profile, schedule, attendance, and reports |

### Role hierarchy notes

- A user may hold **one primary portal role** (`coach`, `parent`, or `player`) plus optionally `admin` for staff who also coach
- `parent` and `player` roles are assigned when a guardian or player account is linked during activation
- The legacy `user` and `editor` roles are **removed** after migration; `editor` permissions map to `coach`

---

## Permission catalog

Permissions follow the `{module}.{action}` naming convention. All permissions use guard `web`.

### Dashboard & analytics

| Permission | Description |
|------------|-------------|
| `dashboard.view` | Access admin or member dashboard home |
| `analytics.view` | View analytics and reports module |

### Players

| Permission | Description |
|------------|-------------|
| `players.view` | List and view player profiles |
| `players.create` | Create players manually (admin override) |
| `players.update` | Edit player profiles |
| `players.delete` | Soft-delete players |

### Registrations

| Permission | Description |
|------------|-------------|
| `registrations.view` | View registration queue and detail |
| `registrations.create` | Create registrations on behalf of guardians |
| `registrations.update` | Edit pending registrations |
| `registrations.approve` | Approve registrations (triggers payment email) |
| `registrations.reject` | Reject registrations (triggers rejection email) |
| `registrations.delete` | Soft-delete registrations |

### Programs & seasons

| Permission | Description |
|------------|-------------|
| `programs.view` | View programs |
| `programs.manage` | CRUD programs |
| `seasons.view` | View seasons |
| `seasons.manage` | CRUD seasons |

### Training & attendance

| Permission | Description |
|------------|-------------|
| `training_sessions.view` | View training schedule |
| `training_sessions.manage` | CRUD training sessions |
| `attendance.view` | View attendance records |
| `attendance.manage` | Mark and edit attendance |

### Performance

| Permission | Description |
|------------|-------------|
| `performance.view` | View performance reports |
| `performance.manage` | Create and edit performance reports |

### Payments

| Permission | Description |
|------------|-------------|
| `payments.view` | View payment history (scoped by role) |
| `payments.manage` | Record manual payments, view all academy payments |
| `payments.pay` | Initiate Paystack payment for own fees |

### Coaches

| Permission | Description |
|------------|-------------|
| `coaches.view` | View coach profiles |
| `coaches.manage` | CRUD coaches |

### Tournaments

| Permission | Description |
|------------|-------------|
| `tournaments.view` | View tournaments |
| `tournaments.manage` | CRUD tournaments |
| `tournaments.squads` | Manage tournament squad assignments |

### Communications

| Permission | Description |
|------------|-------------|
| `announcements.view` | View announcements |
| `announcements.manage` | CRUD and broadcast announcements |
| `communications.receive` | Receive in-app and email communications |

### Content & media

| Permission | Description |
|------------|-------------|
| `pages.manage` | CRUD CMS pages and page sections |
| `gallery.manage` | CRUD gallery items |
| `media.manage` | Full media library access |
| `media.upload` | Upload media (limited, coach scope) |

### Player documents

| Permission | Description |
|------------|-------------|
| `documents.view` | View player documents |
| `documents.upload` | Upload documents for own child |
| `documents.verify` | Verify or reject uploaded documents |
| `documents.manage` | Full document CRUD |

### Administration

| Permission | Description |
|------------|-------------|
| `settings.manage` | Site settings, branding, contact info |
| `staff.manage` | User and role management |
| `audit.view` | View admin audit trail |

---

## Role-to-permission assignments

### Super Admin / Admin (`admin` role)

All permissions in the catalog above.

```text
dashboard.view, analytics.view,
players.view, players.create, players.update, players.delete,
registrations.view, registrations.create, registrations.update, registrations.approve, registrations.reject, registrations.delete,
programs.view, programs.manage, seasons.view, seasons.manage,
training_sessions.view, training_sessions.manage,
attendance.view, attendance.manage,
performance.view, performance.manage,
payments.view, payments.manage,
coaches.view, coaches.manage,
tournaments.view, tournaments.manage, tournaments.squads,
announcements.view, announcements.manage,
pages.manage, gallery.manage, media.manage,
documents.view, documents.verify, documents.manage,
settings.manage, staff.manage, audit.view
```

Super admin (`is_super_admin = true`) is required for `staff.manage` and `settings.manage` if split in Phase 2.

### Coach (`coach` role)

```text
dashboard.view, analytics.view,
players.view,
registrations.view,
programs.view, seasons.view,
training_sessions.view, training_sessions.manage,
attendance.view, attendance.manage,
performance.view, performance.manage,
coaches.view,
tournaments.view, tournaments.squads,
announcements.view,
communications.receive,
documents.view,
media.upload
```

**Explicitly excluded:** `payments.*`, `settings.manage`, `staff.manage`, `audit.view`, `pages.manage`, `gallery.manage`, `registrations.approve`, `registrations.reject`

Coach squad scoping: `training_sessions.manage` and `attendance.manage` apply only to sessions where `coach_id` matches the logged-in coach's profile (enforced in policies).

### Parent (`parent` role)

```text
dashboard.view,
players.view,
registrations.view,
programs.view, seasons.view,
training_sessions.view,
attendance.view,
performance.view,
payments.view, payments.pay,
coaches.view,
tournaments.view,
announcements.view,
communications.receive,
documents.view, documents.upload
```

**Row-level scope:** Policies restrict all `*.view` permissions to records linked via `guardian_id` matching the authenticated user's guardian profile.

### Player (`player` role)

```text
dashboard.view,
players.view,
programs.view, seasons.view,
training_sessions.view,
attendance.view,
performance.view,
payments.view,
coaches.view,
tournaments.view,
announcements.view,
communications.receive,
documents.view
```

**Row-level scope:** Policies restrict to `player_id` matching the authenticated user's player profile.

---

## Module access matrix

Phase 1 scope — high-level capability per role.

| Module | Super Admin | Admin | Coach | Parent | Player |
|--------|:-----------:|:-----:|:-----:|:------:|:------:|
| **Players** | CRUD | CRUD | View assigned squads | View own child | View self |
| **Registrations** | CRUD + approve/reject | CRUD + approve/reject | View | View own submissions | — |
| **Programs** | CRUD | CRUD | View | View | View |
| **Seasons** | CRUD | CRUD | View active | View | View |
| **Training sessions** | CRUD | CRUD | CRUD own | View child schedule | View own |
| **Session attendance** | CRUD | CRUD | Create/mark | View child | View own |
| **Performance reports** | CRUD | CRUD | Create | View child | View own |
| **Payments** | CRUD + record manual | CRUD + record manual | None | View/pay own | View own |
| **Coaches** | CRUD | CRUD | View | View public | View public |
| **Tournaments** | CRUD | CRUD | View + squad manage | View | View |
| **Announcements** | CRUD + broadcast email | CRUD + broadcast email | View | View | View |
| **Communications** | CRUD | CRUD | None | Receive | Receive |
| **Gallery / CMS pages** | CRUD | CRUD | None | Public read | Public read |
| **Player documents** | CRUD + verify | CRUD + verify | View | Upload own child | View own |
| **Media library** | CRUD | CRUD | Upload limited | None | None |
| **Site settings** | CRUD | CRUD* | None | None | None |
| **Staff / audit** | CRUD | View* | None | None | None |
| **Analytics** | View | View | View squad stats | None | None |

\*Admin without `is_super_admin` may have read-only access to staff/audit and limited settings — configurable in `PowerblinkPermissionsSeeder`.

### Legend

| Symbol | Meaning |
|--------|---------|
| CRUD | Create, read, update, soft-delete |
| View | Read-only |
| Create/mark | Create attendance records for assigned sessions |
| View/pay own | View payment history and initiate Paystack for own fees |
| Public read | Unauthenticated access on marketing site |
| — | No access |

---

## Authorization implementation notes

### Admin navigation filtering

Admin sidebar items in `resources/views/layouts/admin.blade.php` (later `layouts/admin-portal.blade.php`) are driven by a config array:

```php
// config/powerblink-admin-nav.php (to be created in Phase 1)
[
    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'permission' => 'dashboard.view'],
    ['label' => 'Players', 'route' => 'admin.players.index', 'permission' => 'players.view'],
    // ...
]
```

Each item is rendered only if `auth()->user()->can($permission)`.

### Member portal routing

`AppLayout` component routes authenticated users by role:

| Role | Default redirect |
|------|------------------|
| `admin` | `/admin/dashboard` |
| `coach` | `/coach/dashboard` |
| `parent` | `/parent/dashboard` |
| `player` | `/player/dashboard` |

### Policy examples (Phase 1)

| Model | Policy rule |
|-------|-------------|
| `Player` | Parent: `guardian_id` matches; Player: `user_id` matches; Coach: player in coach's program sessions |
| `Registration` | Parent: `guardian_id` matches; Admin: `registrations.*` permissions |
| `PerformanceReport` | Parent/Player: linked `player_id`; Coach: `coach_id` matches or squad assignment |
| `PlayerDocument` | Parent: upload for own child; Admin: `documents.verify` |

### Permission seeder

Create `database/seeders/PowerblinkPermissionsSeeder.php` to:

1. Remove legacy permissions: `products.manage`, `categories.manage`, `variants.manage`, `orders.manage`, `customers.view`, `customers.manage`
2. Create all academy permissions listed above
3. Assign permissions to `admin` and `coach` roles
4. Create `parent` and `player` roles with scoped permissions
5. Call `app(PermissionRegistrar::class)->forgetCachedPermissions()` after sync

---

## Migration from ecommerce roles

| Legacy | Action | New |
|--------|--------|-----|
| `admin` role | Keep | `admin` with new permission set |
| `editor` role | Rename + re-permission | `coach` |
| `user` role | Remove | `parent` or `player` as appropriate |
| `products.manage` | Delete permission | `programs.manage` |
| `orders.manage` | Delete permission | `payments.manage` |
| `categories.manage` | Delete permission | — |
| `variants.manage` | Delete permission | — |
| `customers.view/manage` | Delete permission | `players.view` |

Demo seeder creates four test accounts:

| Email | Role | Purpose |
|-------|------|---------|
| `admin@powerblinkfc.com` | `admin` + super | Full admin testing |
| `coach@powerblinkfc.com` | `coach` | Coach portal testing |
| `parent@powerblinkfc.com` | `parent` | Parent portal testing |
| `player@powerblinkfc.com` | `player` | Player portal testing |

---

## Related documents

- [01-database-erd.md](./01-database-erd.md) — Schema and user/guardian/player relationships
- [03-application-flows.md](./03-application-flows.md) — Registration approval and portal flows
- [04-migration-plan.md](./04-migration-plan.md) — Permissions seeder migration step
