# MyAuto-Torque (Laravel backend)

Full **site behavior, feature list, and codebase map**: see **[docs/SITE_AND_CODEBASE.md](docs/SITE_AND_CODEBASE.md)**.

## Requirements

- PHP 8.2+
- Composer
- Node.js (for Vite)

## Environment

Copy `.env.example` to `.env`. **All mail and DB settings are intended to live in `.env`** (Laravel reads them through `config/*.php`).

### Database (production: MySQL)

Set `DB_CONNECTION=mysql` and your `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

Create tables on the server:

```bash
php artisan migrate --force
```

If migrate fails with **“Specified key was too long; max key length is 1000 bytes”** on cPanel/MySQL, ensure `AppServiceProvider` sets `Schema::defaultStringLength(191)` (included in this repo), redeploy, then on a **new empty database** run `php artisan migrate:fresh --force`. If the first migrate attempt stopped halfway, drop partial tables or use `migrate:fresh` before retrying.
php artisan db:seed --force
php artisan storage:link
```

(Optional) Export **MySQL** `CREATE TABLE` statements after migrations (no `mysqldump` required):

```bash
php artisan schema:export-mysql --path=database/schema.mysql.sql
```

The file `database/schema.sql` is **SQLite-only** (for local dev when `DB_CONNECTION=sqlite`). It is not “empty”; it uses SQLite syntax (`AUTOINCREMENT`, quoted identifiers). MySQL will not run that file.

### Outbound mail (PHPMailer SMTP)

Transactional email from the app (contact, inquiries, OTP, password reset, etc.) is sent with **PHPMailer** using **`MAIL_PHPMAILER_*`** and the global from address **`MAIL_FROM_ADDRESS`** / **`MAIL_FROM_NAME`**. Admins can send a test message from **Admin → Site settings**.

| Purpose | Environment variables |
| --- | --- |
| PHPMailer SMTP | `MAIL_PHPMAILER_ENABLED`, `MAIL_PHPMAILER_HOST`, `MAIL_PHPMAILER_PORT`, `MAIL_PHPMAILER_USERNAME`, `MAIL_PHPMAILER_PASSWORD`, `MAIL_PHPMAILER_ENCRYPTION` |
| From + admin inbox | `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`, `MAIL_TO_ADMIN` |

You do not need `MAIL_MAILER`, `MAIL_HOST`, or `MAIL_PORT` in `.env` for this app’s outbound mail; omit them unless you add code that uses Laravel’s `Mail` facade (then set `MAIL_MAILER` and the matching Laravel mailer keys as usual).

## Install and run locally

```bash
composer install
npm install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
npm run dev
php artisan serve
```

`php artisan storage:link` is required for uploaded vehicle gallery images because listing photos are stored on Laravel's public disk and served from `/storage/...`.

## Default seeded users

Defined in `database/seeders/DemoData.php` (run via `DatabaseSeeder`):

- Admin: `admin@example.com` / `password`
- User: `demo@example.com` / `password`

## Deploy via GitHub / cPanel (no npm on server)

This project **builds frontend assets locally** and **commits the compiled output** so production only needs PHP + Composer.

| In Git | Not in Git (never upload) |
| --- | --- |
| `public/build/` (Vite manifest + JS/CSS) | `node_modules/` |
| Application code, `vendor/` if you commit it | `public/hot` (dev server only) |
| `.env.example` (template) | `.env` (create on server) |

### Before you push (developer machine)

Whenever `resources/css`, `resources/js`, or Tailwind/Vite config change:

```bash
npm ci
npm run build
git add public/build
git commit -m "Build frontend assets for production"
git push
```

`public/build` is **not** gitignored — it is the production asset bundle Laravel serves via `@vite` in production mode.

### On the server (cPanel / SSH)

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

You do **not** need `npm install` or `npm run build` on the server if `public/build` is already in the deployed branch.

### `.env` on the server

Copy from `.env.example`, set `APP_KEY`, `DB_*`, mail, and Paystack. `.env` stays out of Git.
