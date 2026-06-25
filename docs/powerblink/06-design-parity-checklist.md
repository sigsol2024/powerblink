# PowerBlink FC — Design Parity Checklist

Phase B design parity against Stitch screens in `new designs/Powerblink_academy_management_platform/*/code.html`.

**Brand tokens:** navy `#0b1c34`, green `#006d32`, gold `#f4c542` (tertiary-fixed), Montserrat headings, Inter body, Bebas Neue stats.

**Shared acceptance (all public pages):**
- [x] Single site nav via `layouts/site.blade.php` + `partials/powerblink/public-header` (no duplicate headers in page content)
- [x] Elite Performance Tailwind tokens in `partials/powerblink/theme.blade.php`
- [x] Glass/blur fixed header, green active nav underline
- [x] `primary-container` footer with quick links, programs, contact columns
- [x] Images use `asset/images/powerblink/*` via `PlaceholderMedia` / `MediaImageUrl`
- [x] CMS section bindings via `PageController::pageSections()` where applicable

---

## Public pages (Phase B — implemented)

| Screen | Stitch folder | Blade view | Status | Acceptance criteria |
|--------|---------------|------------|--------|---------------------|
| Home | `home_powerblink_fc` | `pages/powerblink/home.blade.php` | **Implemented** | Cinematic full-viewport hero with stats grid; bento “Why Our Academy” cards; about preview band; program pathway cards from DB; coach spotlight; tournament highlight collage; green final CTA; fade-up animations |
| About | `about_us_powerblink_fc` | `pages/powerblink/about.blade.php` | **Implemented** | Cinematic hero; mission/vision/values cards; philosophy + image split; timeline journey; leadership grid from `LeadershipMember`; registration CTA band |
| Programs | `programs_powerblink_fc` | `pages/powerblink/programs.blade.php` | **Implemented** | Navy cinematic hero; bento-style program cards with fees/sessions; contact CTA band |
| Coaching | `coaching_team_powerblink_fc` | `pages/powerblink/coaching.blade.php` | **Implemented** | Hero band; featured head coach spotlight; licensed staff card grid from `Coach` model |
| Gallery | `gallery_powerblink_fc` | `pages/powerblink/gallery.blade.php` | **Implemented** | Hero band; category filter chips (Alpine); masonry grid; hover captions |
| Contact | `contact_us_powerblink_fc` | `pages/powerblink/contact.blade.php` | **Implemented** | Hero; three inquiry cards; navy form section with AJAX submit; quote sidebar; map overlay card |
| FAQ | contact/about patterns | `pages/powerblink/faq.blade.php` | **Implemented** | PowerBlink cinematic hero; accordion categories; academy support CTA; no legacy automotive copy |
| Tournaments (public) | home tournament section | `pages/powerblink/tournaments.blade.php` | **Implemented** | Cinematic hero; collage grid; tournament cards from DB |

---

## Registration & payment (Phase B — implemented)

| Screen | Stitch folder | Blade / route | Status | Acceptance criteria |
|--------|---------------|---------------|--------|---------------------|
| Registration wizard | `registration_powerblink_fc` | `registration.wizard` | **Implemented** | Task-focused `layouts/registration` (no public nav/footer); 6-step progress indicator; styled form sections; program radio cards from `Program`; payment plan toggles; review summary; back/continue controls |
| Payment / pay | — | `registration.pay` | **Implemented** | Payment summary card; Paystack CTA; secure checkout messaging |
| Complete | — | `registration.complete` | **Implemented** | Success state with reference code |
| Invalid link | — | `registration.payment-invalid` | **Implemented** | Error state on registration layout |

---

## Member & admin portals (Phase B — implemented)

| Screen | Stitch folder | Blade / route | Status | Acceptance criteria |
|--------|---------------|---------------|--------|---------------------|
| Player dashboard | `player_dashboard_powerblink_fc` | `portal.dashboard` (player) | **Implemented** | Cinematic welcome hero; stat cards (attendance, program, sessions); upcoming sessions pipeline; profile band; attendance table; announcements from DB |
| Parent dashboard | — | `portal.dashboard` (parent) | **Implemented** | Welcome band; children/registrations stats; linked players + registration pipeline; announcements |
| Coach dashboard | — | `portal.dashboard` (coach) | **Implemented** | Coach hero; squad/session stats; upcoming sessions table; coach profile card |
| Admin dashboard | `admin_dashboard_powerblink_fc` | `admin.dashboard` | **Implemented** | KPI stat cards; recent registrations table; traffic summary; quick actions — all from Eloquent |
| Player management | `player_management_powerblink_fc` | `admin.players.*` | **Implemented** | `pb-admin-table` responsive list; status pills; touch-friendly actions |
| Programs management | `programs_management_powerblink_fc` | `admin.programs.*` | **Implemented** | CRUD table; season/active columns |
| Coaching team mgmt | `coaching_team_management_powerblink_fc` | `admin.coaches.*` | **Implemented** | Staff list; license badges |
| Registrations | `registrations_powerblink_fc` | `admin.registrations.*` | **Implemented** | Status filter chips; pipeline statuses; approve/reject actions |
| Attendance | `attendance_tracking_powerblink_fc` | `admin.attendance.*` | **Implemented** | Session filter; roster status table |
| Training schedule | `training_schedule_powerblink_fc` | `admin.training-sessions.*` | **Implemented** | Date/program/coach columns; schedule CTA |
| Tournament mgmt | `tournament_management_powerblink_fc` | `admin.tournaments.*` | **Implemented** | Fixture list with status pills |
| Financial mgmt | `financial_management_powerblink_fc` | `admin.payments.*` | **Implemented** | Tabbed ledger; summary stat cards; amount formatting |
| Communications | `communications_center_powerblink_fc` | `admin.announcements.*` | **Implemented** | Announcement list; compose CTA |
| Performance analytics | `performance_analytics_powerblink_fc` | `admin.performance.*` | **Implemented** | Report list with overall scores |
| Analytics (extended) | `performance_analytics_powerblink_fc` | `admin.analytics.*` | **Implemented** | PowerBlink KPI cards, toolbar, charts; legacy luxe/anx palette removed |

---

## Mobile audit (priority screens — June 2026 remediation)

Test viewports: **375, 390, 412, 768, 1024, 1440px**

| Screen | Mobile | Desktop | Notes |
|--------|--------|---------|-------|
| Home | Pass | Pass | Responsive hero min-heights; stat grid 2-col on phone |
| Registration wizard | Pass | Pass | `px-4` padding; 44px touch targets; progress bar |
| Player dashboard | Pass | Pass | Stat cards stack; session list readable |
| Parent dashboard | Pass | Pass | Stat cards stack; children list |
| Admin dashboard | Pass | Pass | KPI grid; horizontal table scroll |

Shared: `pb-mobile-safe` on site/registration layouts; `overflow-x-hidden` in theme-styles.

---

## Tournaments (public — removed partial status)

Previously partial — now **Implemented** (see public pages table above).

---

## Partials & layout (Phase B)

| Asset | Path | Status |
|-------|------|--------|
| Theme tokens | `partials/powerblink/theme.blade.php` | Implemented |
| Utility styles | `partials/powerblink/theme-styles.blade.php` | Implemented — includes `pb-admin-table`, wizard `input-focus`, glass cards |
| Public header | `partials/powerblink/public-header.blade.php` | Implemented |
| Public footer | `partials/powerblink/public-footer.blade.php` | Implemented |
| Registration header | `partials/powerblink/registration-header.blade.php` | Implemented |
| Registration progress | `partials/powerblink/registration-progress.blade.php` | Implemented |
| Dashboard stat card | `partials/powerblink/dashboard-stat-card.blade.php` | Implemented |
| Admin/member shell | `layouts/admin-portal.blade.php`, `layouts/member-portal.blade.php` | Implemented — single `dashboard-header` title; `x-admin.page-header` actions-only (no double header) |
| Registration layout | `layouts/registration.blade.php` | Implemented — task-focused, no duplicate nav |
| Site layout | `layouts/site.blade.php` | Implemented — unified `pt-20`, minimal header for `registration.*` on legacy paths |

---

## Verification steps

1. Run `php artisan serve` and visit `/`, `/about`, `/programs`, `/coaching`, `/gallery`, `/contact`.
2. Visit `/registration` — confirm single branding header (no public nav/footer on registration layout).
3. Log in as admin — confirm one top bar title in portal shell (no duplicate page header bar).
4. Resize to mobile — hamburger drawer, horizontal scroll tables, min 44px touch targets on actions.
5. With demo data seeded, confirm dashboards and tables render live counts from DB.

---

*Last updated: Pre-deployment remediation — June 2026*
