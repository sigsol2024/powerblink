# PowerBlink FC — Migration Plan

**Document:** `04-migration-plan.md`  
**Version:** 1.0  
**Status:** Phase 0 specification (pre-implementation)  
**Last updated:** 2026-06-24

This document defines how to migrate the Vogue Dress Laravel ecommerce codebase to PowerBlink FC Academy Management System: which database tables to keep, remove, and create; which application files to delete; and the ordered execution sequence.

**Critical rule:** Do **not** alter the existing `payments` table schema at any point. Academy Paystack activity uses new `registration_payments` and `academy_payments` tables. Legacy `payments` is dropped together with `orders` and `order_items` when ecommerce is removed.

---

## Table of contents

1. [Migration strategy](#migration-strategy)
2. [KEEP — tables and modules](#keep--tables-and-modules)
3. [REMOVE — tables and modules](#remove--tables-and-modules)
4. [CREATE — new academy tables](#create--new-academy-tables)
5. [RENAME / REPURPOSE](#rename--repurpose)
6. [File deletion list](#file-deletion-list)
7. [Migration execution order](#migration-execution-order)
8. [Post-migration verification](#post-migration-verification)
9. [Rollback considerations](#rollback-considerations)

---

## Migration strategy

The migration follows a **create-first, drop-second** approach:

1. Add all academy tables alongside existing ecommerce tables
2. Seed demo data into academy tables
3. Drop ecommerce tables in a single migration
4. Remove dead application code and routes
5. Rebrand configuration and email templates

This avoids a dual-read period on the `payments` table and eliminates schema alteration risk on FK-locked ecommerce tables.

---

## KEEP — tables and modules

| Asset | Action | Notes |
|-------|--------|-------|
| `users` | Keep | Add nullable profile fields if needed; `is_super_admin` already exists |
| `sessions` | Keep | Laravel session driver |
| `password_reset_tokens` | Keep | Breeze password reset |
| Spatie permission tables | Keep | `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions` — replace permission names in seeder |
| `media` | Keep + extend | Add nullable `category`, `alt_text` columns |
| `cms_pages` | Keep | Reseed with PowerBlink page slugs |
| `page_sections` | Keep | Reseed with academy copy |
| `site_settings` | Keep | Rebrand keys and values |
| `admin_audit_trails` | Keep | Admin action logging |
| `site_traffic_events` | Keep | Analytics |
| `payments` | Keep until drop | **Never alter schema** — FK-locked to `orders` |
| `orders` | Keep until drop | Dropped with `payments` |
| `order_items` | Keep until drop | Dropped with `orders` |
| `jobs`, `job_batches`, `failed_jobs` | Keep | Queue infrastructure |
| `cache`, `cache_locks` | Keep | Cache infrastructure |
| Auth stack (Breeze) | Keep | Login, registration, password reset |
| `PaystackService` | Keep | Rewire to academy payment models |
| `OutboundMailService` | Keep | Registration email templates |
| `AdminAuditTrail` model | Keep | |
| Admin layout foundation | Keep | Refactor to `admin-portal` layout |

---

## REMOVE — tables and modules

Remove **after** academy tables exist and demo seed succeeds.

### Database tables (single drop migration)

| Table | Dependencies | Notes |
|-------|--------------|-------|
| `vehicle_images` | `vehicles` | Product gallery images |
| `vehicle_variants` | `vehicles` | Size/color variants |
| `vehicle_favorites` | `vehicles`, `users` | User favorites |
| `vehicle_inquiries` | `vehicles` | Product inquiries |
| `vehicles` | — | Core product table |
| `order_items` | `orders`, `vehicles` | Line items |
| `payments` | `orders` | **Dropped unchanged** with orders |
| `orders` | — | Checkout orders |
| `vendor_profiles` | `users` | Vendor marketplace profiles |
| `listing_options` | `listing_option_categories` | Product attributes |
| `listing_option_categories` | — | Attribute categories |

**Drop order within migration:** child tables first (`order_items`, `vehicle_images`, `vehicle_variants`, `vehicle_favorites`, `vehicle_inquiries`, `payments`, `listing_options`), then parent tables (`orders`, `vehicles`, `vendor_profiles`, `listing_option_categories`).

### Application modules to remove

| Domain | Controllers | Models | Support classes |
|--------|-------------|--------|-----------------|
| Products/Vehicles | `AdminVehicleController`, `UserVehicleController`, `VehicleInquiryController` | `Vehicle`, `VehicleImage`, `VehicleVariant`, `VehicleInquiry` | `VehicleListingCatalog`, `VehicleImageUrl`, `InteractsWithVehicleForms` |
| Cart/Checkout | `CartController`, `CheckoutController` | — | `Cart`, `CheckoutPaymentMethods` |
| Orders | `AdminOrderController`, `OrderLookupController`, `OrderTrackingController` | `Order`, `OrderItem`, `Payment` | `OrderPaymentCompletionService` |
| Compare/Favorites | `CompareController`, `FavoriteController` | — | `Compare` |
| Listing options | `AdminListingOptionController`, `AdminCategoryController`, `AdminVariantController`, `ListingOptionLookupController` | `ListingOption`, `ListingOptionCategory` | `ListingOptionCatalogSync`, `ListingOptionNormalizer` |
| Vendor | `VendorSettingsController` | `VendorProfile` | `VendorProfilePolicy`, `VendorIdleTimeout` middleware |

---

## CREATE — new academy tables

All tables defined in [01-database-erd.md](./01-database-erd.md). Migration file: `2026_06_24_100000_create_academy_tables.php` (or equivalent).

| Table | SoftDeletes |
|-------|-------------|
| `seasons` | Yes |
| `programs` | Yes |
| `guardians` | Yes |
| `registrations` | Yes |
| `players` | Yes |
| `coaches` | Yes |
| `training_sessions` | Yes |
| `session_attendance` | No |
| `performance_reports` | No |
| `registration_payments` | No |
| `academy_payments` | No |
| `installment_plans` | No |
| `player_documents` | No |
| `tournaments` | Yes |
| `tournament_squads` | No |
| `announcements` | No |
| `gallery_items` | No |
| `leadership_members` | Yes |
| `timeline_events` | No |

### Additional migrations

| Migration | Purpose |
|-----------|---------|
| `2026_06_24_*_extend_media_table.php` | Add `category`, `alt_text` nullable columns |
| `2026_06_24_*_create_notifications_table.php` | Laravel standard notifications |
| `2026_06_24_*_drop_ecommerce_tables.php` | Drop all ecommerce tables listed above |

---

## RENAME / REPURPOSE

| Legacy | New | Notes |
|--------|-----|-------|
| `Payment` model | **Delete** | Replace with `RegistrationPayment`, `AcademyPayment` |
| `OrderPaymentCompletionService` | **Delete** | Replace with `RegistrationPaymentCompletionService` |
| `editor` Spatie role | `coach` | New permission set |
| `admin` permission set | Academy permissions | See [02-role-permissions.md](./02-role-permissions.md) |
| `user` Spatie role | **Remove** | Replace with `parent` / `player` |
| `PageController::inventory()` | Academy public pages | `/programs`, `/coaching`, etc. |
| `PageController::vehicleShow()` | **Delete** | |
| `AdminPageController::editablePages()` | PowerBlink CMS schemas | Replace Vogue field definitions |
| `PaystackWebhookController` | Extend | Route academy refs to `RegistrationPaymentCompletionService` |
| `PermissionsSeeder` | `PowerblinkPermissionsSeeder` | Academy permission names |
| `layouts/admin.blade.php` | `layouts/admin-portal.blade.php` | PowerBlink sidebar |
| `layouts/site.blade.php` | Refactor | PowerBlink public header/footer |
| `partials/luxe-*` | `partials/powerblink/*` | Elite Performance design tokens |

---

## File deletion list

Delete or gut these files during Phase 1 strip-ecommerce step. Verify no remaining references before deletion.

### Models (`app/Models/`)

```
Vehicle.php
VehicleImage.php
VehicleVariant.php
VehicleInquiry.php
Order.php
OrderItem.php
Payment.php
VendorProfile.php
ListingOption.php
ListingOptionCategory.php
```

### Controllers (`app/Http/Controllers/`)

```
AdminVehicleController.php
UserVehicleController.php
VehicleInquiryController.php
CartController.php
CheckoutController.php
AdminOrderController.php
OrderLookupController.php
OrderTrackingController.php
CompareController.php
FavoriteController.php
AdminListingOptionController.php
AdminCategoryController.php
AdminVariantController.php
ListingOptionLookupController.php
VendorSettingsController.php
```

### Controller concerns (`app/Http/Controllers/Concerns/`)

```
InteractsWithVehicleForms.php
```

### Services (`app/Services/`)

```
OrderPaymentCompletionService.php
```

### Support (`app/Support/`)

```
Cart.php
Compare.php
CheckoutPaymentMethods.php
VehicleListingCatalog.php
VehicleImageUrl.php
ListingOptionCatalogSync.php
ListingOptionNormalizer.php
```

### Policies (`app/Policies/`)

```
VendorProfilePolicy.php
```

### Middleware (`app/Http/Middleware/`)

```
VendorIdleTimeout.php
```

### Views — ecommerce pages (`resources/views/pages/`)

```
cart/index.blade.php
checkout/index.blade.php
compare.blade.php
favorites/index.blade.php
inventory/          (entire directory)
orders/             (entire directory)
```

### Views — dashboard ecommerce (`resources/views/dashboard/`)

```
vendor-settings.blade.php
vehicles/           (if present)
```

### Views — admin ecommerce (`resources/views/admin/`)

```
vehicles/           (if present)
orders/             (if present)
categories/         (if present)
variants/           (if present)
listing-options/    (if present)
```

### Partials — Vogue ecommerce chrome (`resources/views/partials/`)

```
luxe-cart-widget.blade.php
luxe-store-header.blade.php    (replace, do not keep alongside)
luxe-home-footer.blade.php     (replace)
luxe-shop-footer.blade.php     (replace)
luxe-public-theme.blade.php    (replace with powerblink theme)
```

### Routes to remove (`routes/web.php`)

| Route pattern | Name(s) |
|---------------|---------|
| `/shop` | `shop.index` |
| `/product/{slug}` | `product.show` |
| `/inventory/{slug?}` | `inventory.show` |
| `/inventory/{slug}/inquiry` | inquiry store |
| `/cart/*` | `cart.*` |
| `/checkout/*` | `checkout.*` |
| `/compare/*` | `compare.*` |
| `/favorites/*` | `favorites.*` |
| `/dashboard/vehicles/*` | `dashboard.vehicles.*` |
| `/admin/vehicles/*` | `admin.vehicles.*` |
| Order tracking/lookup routes | `orders.*` |

### Seeders to update (not delete)

| Seeder | Action |
|--------|--------|
| `PermissionsSeeder.php` | Replace with `PowerblinkPermissionsSeeder` or gut ecommerce permissions |
| `DatabaseSeeder.php` | Call `PowerblinkDemoSeeder` instead of vehicle/order seeders |
| Vehicle/order-specific seeders | Remove calls |

### Database seed data to remove

```
database/seed-data/   (ecommerce-specific manifests, if any)
Stylemix import migrations (historical — leave migration files, tables dropped)
```

---

## Migration execution order

Execute in this exact sequence. Do not skip steps.

### Step 1 — Create academy schema

```
php artisan migrate --path=database/migrations/2026_06_24_100000_create_academy_tables.php
```

Creates: all academy tables + `registration_payments` + `academy_payments` with `SoftDeletes` where specified.

### Step 2 — Extend media table

```
php artisan migrate --path=database/migrations/2026_06_24_*_extend_media_table.php
```

Adds `category`, `alt_text` to `media`.

### Step 3 — Create notifications table

```
php artisan migrate --path=database/migrations/2026_06_24_*_create_notifications_table.php
```

### Step 4 — Seed demo data

```
php artisan powerblink:import-design-assets   # if asset manifest ready
php artisan db:seed --class=PowerblinkDemoSeeder
php artisan db:seed --class=PowerblinkPermissionsSeeder
```

Verify academy tables populated; ecommerce tables still present but unused.

### Step 5 — Drop ecommerce tables

```
php artisan migrate --path=database/migrations/2026_06_24_*_drop_ecommerce_tables.php
```

Drops: `vehicles`, `vehicle_images`, `vehicle_variants`, `vehicle_favorites`, `vehicle_inquiries`, `orders`, `order_items`, `payments`, `vendor_profiles`, `listing_option_categories`, `listing_options`.

**Do not** run any migration that alters `payments` before this drop.

### Step 6 — Remove dead code

- Delete files listed in [File deletion list](#file-deletion-list)
- Remove ecommerce routes from `routes/web.php`
- Remove ecommerce nav items from admin layout
- Delete `Payment`, `Order`, `Vehicle` model imports across codebase
- Run `composer dump-autoload`

### Step 7 — Rebrand configuration

- Update `.env.example`: `APP_NAME=PowerBlink FC`, URLs, mail from address
- Reseed `site_settings` with PowerBlink branding
- Update email templates in `resources/views/emails/`
- Replace Vogue CMS field definitions in `AdminPageController`

### Step 8 — Verify

```bash
php artisan route:list          # No /shop, /cart, /checkout routes
php artisan migrate:status      # All migrations ran
php artisan db:seed             # Demo data intact
```

---

## Post-migration verification

| Check | Expected result |
|-------|-----------------|
| `php artisan migrate:status` | All migrations green |
| `SELECT COUNT(*) FROM seasons` | ≥ 1 (demo season) |
| `SELECT COUNT(*) FROM vehicles` | Table does not exist |
| `SELECT COUNT(*) FROM payments` | Table does not exist |
| `SELECT COUNT(*) FROM registration_payments` | ≥ 0 (demo payments) |
| `php artisan route:list \| grep shop` | No results |
| Admin login | Dashboard loads with academy nav |
| Paystack webhook test | Writes to `registration_payments` only |

---

## Rollback considerations

| Scenario | Approach |
|----------|----------|
| Academy migration fails before ecommerce drop | `php artisan migrate:rollback` on academy migrations only; ecommerce intact |
| Ecommerce drop executed | **No automatic rollback** — restore from database backup (see [05-backup-recovery.md](./05-backup-recovery.md)) |
| Partial code deletion | Git revert; database unchanged |

**Always take a full database backup immediately before Step 5 (ecommerce drop).**

---

## Related documents

- [01-database-erd.md](./01-database-erd.md) — Full table definitions
- [02-role-permissions.md](./02-role-permissions.md) — Permission seeder changes
- [03-application-flows.md](./03-application-flows.md) — New routes and controllers
- [05-backup-recovery.md](./05-backup-recovery.md) — Backup before migration Step 5
