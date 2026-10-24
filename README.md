# School ERP - Admin app (standalone)

Full control: students, teachers, classes, subjects, timetable, attendance, exams, marks, fees, invoices, receipts, and creating other admins.

This is a complete, independent Laravel project overlay: own routes, controllers, models, views, migrations and seeder. It does not need the other apps to run.

## Install
```bash
unzip school-admin.zip && cd school-admin
bash setup.sh
cd laravel-admin && php artisan serve --port=8000
```
Open http://127.0.0.1:8000 - login `admin@school.test` / `password` (demo data is seeded).

## Database
- **Default: its own database** (SQLite file inside the project). Good for trying the app alone.
- **Shared database:** run `DB_CONNECTION=mysql DB_DATABASE=school_erp DB_USERNAME=root DB_PASSWORD= bash setup.sh` with the same values in every app, so the Admin, Teacher and Student apps see the same data. Migrations are safe to run on a database another app already set up.

Important: apps with separate databases do not share data. For real use (admin adds a student, student sees the fees), point all three apps at one database.

## Admin access
The seeded administrator keeps full access. When creating another admin, select the modules that account can use; the dashboard is always available. Module access is enforced on the server as well as in the navigation. Admin account management remains available only to full-access administrators.

## Deploy on Vercel
This project uses the community `vercel-php` runtime through `revolution/laravel-vercel-installer`. The current `vercel-php@0.9.0` runtime provides PHP 8.5; this project requires PHP `^8.2` and Laravel 12. Vercel is serverless, so production must use a remote MySQL database rather than the local SQLite file.

1. Build the frontend assets with `npm run build`. The generated `public/build` assets are intentionally included in Git so Vercel's static-file builder can serve the files referenced by Laravel's Vite manifest.
2. Push the project to GitHub and import that repository as a Vercel project.
3. Create a MySQL database with a provider reachable from Vercel. Set these values in Vercel **Project Settings → Environment Variables** for Production (and Preview if needed): `APP_KEY`, `APP_URL`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`. Set `DB_CONNECTION=mysql` and `DB_PORT=3306` (these defaults are already in `vercel.json`). Never commit `.env` or database credentials.
4. Generate a fresh key locally with `php artisan key:generate --show` and add the output directly as Vercel's `APP_KEY` environment variable. Do not put the key in source control.
5. From a trusted machine with the production DB environment variables configured, run `php artisan migrate --force`. Seed only if the production database is intended to receive demo records: `php artisan db:seed --force`. Do not use `migrate:fresh` on a production database.
6. Deploy the project from Vercel. `vercel.json` configures cookie-based sessions, in-memory cache, stderr logging, writable compiled views under `/tmp`, MySQL, and trusted proxy handling.

The local `setup.sh` remains for local installs; it uses file sessions and SQLite by default and is not the Vercel production setup. Do not store uploads or other persistent application files on Vercel's local filesystem; use object storage if file uploads are added later.

## Manual install
1. `composer create-project laravel/laravel laravel-admin`
2. Copy `app`, `database`, `resources`, `routes` into it.
3. Set `SESSION_DRIVER=file`, `CACHE_STORE=file`, `SESSION_COOKIE=school_admin_session` in `.env`, then `php artisan key:generate && php artisan migrate --seed`.
