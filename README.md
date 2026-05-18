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
