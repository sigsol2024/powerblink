# PowerBlink FC — Pre-Deploy Re-Audit (June 2026)

Post-remediation checklist after implementing the pre-deployment remediation plan.

## Summary: **GO for staging validation** → production after MySQL smoke test

| Audit | Status | Notes |
|-------|--------|-------|
| SQL table integrity | Pass | All academy tables present; 19/19 migrations |
| Seed density | Pass (after expansion) | Showcase seeder meets minimums via `ShowcaseSeedDensityTest` |
| Fresh-install simulation | Pass (automated) | `FreshInstallSimulationTest` — re-run on staging MySQL |
| Design fidelity | Pass (remediated) | FAQ rebuilt; tournaments/analytics restyled; PowerBlink tokens |
| Mobile priority screens | Pass (remediated) | 375–1440px patterns; `pb-mobile-safe`, responsive grids |
| Image integrity | Pass | `DesignAssetIntegrityTest` — 91/91 JPGs + manifest |
| Legacy references | Pass | Analytics inventory/compare removed; FAQ automotive copy removed |
| DB-driven KPIs | Pass | Home fallbacks `600`/`15` removed |
| Demo logins | Pass | `DemoLoginSmokeTest` |
| Traffic seed URLs | Pass | PowerBlink domain via `config('powerblink.site_url')` |

## Automated regression suite

Run before every deploy:

```bash
php artisan test
```

New tests: `ShowcaseSeedDensityTest`, `FreshInstallSimulationTest`, `DemoLoginSmokeTest`, `HardcodedKpiRemovedTest`, `DesignAssetIntegrityTest`.

## Staging gate (manual)

1. Empty MySQL → import `powerblink_academy.sql`
2. `php artisan migrate --force`
3. Login all four demo roles
4. Mobile check: Home, Registration, Player/Parent/Admin dashboards at 375px

## Regenerate SQL export

```bash
APP_ENV=local php artisan db:export-powerblink-sql --sqlite --fresh
php artisan db:verify-migrations-sync
```

Set `POWERBLINK_SITE_URL=https://your-domain.com` in `.env` before export for correct traffic event URLs.
