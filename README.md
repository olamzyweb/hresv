# HRest – PHP/Tailwind Hotel Booking Demo

Mobile-first hotel reservation system with guest and admin roles, REST-ish PHP APIs, Tailwind UI, Stripe-like payment stub, email notifications, and MySQL schema/migrations.

## Features

- Guest browse/search rooms, date/guest filters, availability check.
- Booking flow with optional account or express checkout, coupon support, price calc, confirmation email + booking reference.
- Auth: register/login, email verification tokens, password reset, role-based access control, CSRF.
- Admin dashboard APIs: bookings list & status updates, rooms & room types CRUD, occupancy/revenue report, coupons, audit logs.
- Payment stub with Stripe-style payload; webhook endpoint for test mode.
- Secure defaults: prepared PDO, bcrypt, sanitized input, secure sessions, CSRF tokens, file upload guard rails.

## Stack

- PHP 8+, MySQL 8, vanilla JS + Tailwind CDN, PHPMailer via Composer.
- Dockerfile + docker-compose for local dev (php-apache + mysql + adminer).

## Quickstart

```bash
cp env.example .env
composer install
docker-compose up -d               # or run your own MySQL
mysql -h 127.0.0.1 -P 3306 -u root -p < jhotel_schema.sql
mysql -h 127.0.0.1 -P 3306 -u root -p < seeds.sql
php -S localhost:8000 -t public    # or use Apache/Nginx pointing to public/
```

Admin sample login (from seeds.sql):

- Email: admin@demo.test
- Password: password

## Environment (.env)

```
APP_ENV=local
APP_URL=http://localhost:8000
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=jhotel
DB_USER=root
DB_PASS=secret
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USER=null
MAIL_PASS=null
MAIL_FROM=hotel@example.com
MAIL_FROM_NAME=HRest Hotel
PAYMENT_PROVIDER=stripe_test
STRIPE_TEST_KEY=pk_test_dummy
STRIPE_TEST_SECRET=sk_test_dummy
SESSION_NAME=hrest_sid
LOG_PATH=storage/logs/app.log
```

## File layout

- `public/` static pages + entrypoints calling fetch APIs.
- `api/` REST endpoints (JSON only).
- `config/` env + bootstrap.
- `lib/` helpers: db, auth, csrf, validation, availability, booking, mailer, payments, logger.
- `storage/logs/` basic log file (git-ignored).
- `jhotel_schema.sql`, `seeds.sql` migrations & sample data.
- `tests/` minimal smoke/integration helpers.

## Security notes

- All SQL via prepared PDO; password_hash (bcrypt) + password_verify.
- Secure HTTPOnly sessions; regenerates on login; CSRF token required on POST/PUT/DELETE.
- Basic rate limiting on auth endpoints using session counters.
- Input validation/sanitization in `lib/validation.php`; output escaped in templates.
- File uploads (room images) check mime/size and store under `storage/uploads/`.

## Running tests

```bash
php tests/smoke.php
```

This runs a few sanity checks against helper functions (no PHPUnit dependency).

## Deployment

- Set production .env values.
- Serve `public/` via web server; ensure `storage/` writable by PHP user.
- Configure SMTP creds for PHPMailer; switch payment provider to live when ready.

## Notes

- Tailwind loaded via CDN for simplicity; swap to a build pipeline if desired.
- Calendar and datepickers use flatpickr CDN on frontend.
- Payment endpoints are stubbed but structured to be replaced with real provider SDKs.
