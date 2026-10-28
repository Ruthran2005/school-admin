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

## Free demo deployment on Vercel
The repository already includes a Vercel configuration using the community `vercel-php` runtime through `revolution/laravel-vercel-installer`. Vercel's generated deployment URL includes HTTPS. This setup is for a demo with fake data only, not real school records: free-plan limits and provider terms can change, and a database's free allowance may be exceeded and become chargeable.

1. Build the frontend assets with `npm run build`. The generated `public/build` assets are intentionally included in Git so Vercel's static-file builder can serve the files referenced by Laravel's Vite manifest.
2. Push the project to GitHub and import that repository as a Vercel project.
3. Create a MySQL-compatible database that is reachable from Vercel. TiDB Cloud Starter currently advertises a free monthly allowance for eligible MySQL-compatible instances; check its current limits, region availability, and billing controls before creating one: [TiDB Cloud pricing](https://www.pingcap.com/pricing/). Set a usage/spend limit if offered. If you cannot prevent charges beyond the free allowance, do not use that database for a no-cost demo.
4. In Vercel **Project Settings → Environment Variables**, set `APP_KEY`, `APP_URL`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`. Set `DB_CONNECTION=mysql` and `DB_PORT=3306` (the defaults are in `vercel.json`). Use the database provider's TLS instructions; if its connection requires a CA certificate, configure `MYSQL_ATTR_SSL_CA` to the deployed CA certificate path. Never disable TLS or commit `.env`, credentials, or database secrets.
5. Generate a fresh key locally with `php artisan key:generate --show` and add the output directly as Vercel's `APP_KEY` environment variable. Do not put the key in source control.
6. From a trusted machine with the demo database environment variables configured, run `php artisan migrate --force` and `php artisan db:seed --force` to create the schema and fake demo records. The seeded demo accounts use published sample passwords, so anyone who can reach the demo can sign in and change its data. Never seed real school information or reuse these credentials.
7. Deploy the project from Vercel. `vercel.json` configures cookie-based sessions, in-memory cache, stderr logging, writable compiled views under `/tmp`, MySQL, and trusted proxy handling. Check the Vercel and database usage dashboards after deployment; this is not a guarantee of zero cost.

The local `setup.sh` remains for local installs; it uses file sessions and SQLite by default and is not the Vercel deployment setup. Do not store uploads or other persistent application files on Vercel's local filesystem; use object storage if file uploads are added later.

## AWS Free Plan EC2 demo
This is a temporary demo setup for fake data only. AWS's current Free plan is for eligible new customers: it starts with $100 in credits, offers up to $100 more for eligible activities, and ends after six months or when credits run out. AWS says you are not charged unless you convert to a Paid plan. Confirm the current terms in your AWS account before launching anything, and do not convert to Paid if you require a no-charge demo. See [AWS Free Tier](https://aws.amazon.com/free/). The account and its resources do not provide an always-free production host.

This single-server setup uses SQLite on the EC2 instance's persistent EBS volume instead of a separately billed managed database. It does not provide HTTPS: use fake data only, and do not enter real student, staff, or school information. For real use, configure a domain and HTTPS and move to a properly secured, backed-up production database.

1. In AWS Console, choose **Free plan** if offered, select **Asia Pacific (Mumbai) (`ap-south-1`)**, and open **EC2 → Launch instance**. Pick Ubuntu Server 24.04 LTS, and select only an instance type and storage configuration that the console explicitly marks as eligible for your Free plan. If no eligible option is shown, stop rather than switching to Paid.
2. Create/download a key pair and keep it private. In the instance security group, allow SSH (TCP 22) only from **My IP** and HTTP (TCP 80) as needed for your demo. Do not open any other ports. Do not allocate an Elastic IP. Enable EBS encryption if offered, and keep the instance's default storage size within the Free plan.
3. Connect using EC2 Instance Connect or SSH, then install the app's runtime:

   ```bash
   sudo apt update
   sudo apt install -y nginx git unzip composer php8.3-cli php8.3-fpm php8.3-sqlite3 php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath
   ```

4. Before deployment, build the frontend assets on your development machine with `npm run build` and commit the generated `public/build` files if they changed. Clone this repository to `/var/www/school-admin` (for a private repository, use a deploy key; never put a personal access token in a clone URL), then install PHP dependencies:

   ```bash
   sudo git clone <repository-HTTPS-URL> /var/www/school-admin
   sudo chown -R ubuntu:ubuntu /var/www/school-admin
   cd /var/www/school-admin
   composer install --no-dev --optimize-autoloader --no-interaction
   sudo cp .env.example .env
   ```

5. Edit `/var/www/school-admin/.env` with `sudo nano .env` and set `APP_NAME="School ERP Admin"`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=http://<EC2-public-IPv4>`, `DB_CONNECTION=sqlite`, `DB_DATABASE=/var/www/school-admin/database/database.sqlite`, `SESSION_DRIVER=file`, `CACHE_STORE=file`, and `QUEUE_CONNECTION=sync`. Create the SQLite file and set writable permissions:

   ```bash
   sudo touch database/database.sqlite
   sudo chown -R www-data:www-data storage bootstrap/cache database
   sudo chmod -R ug+rwX storage bootstrap/cache database
   sudo chown ubuntu:www-data .env
   sudo chmod 640 .env
   sudo php artisan key:generate --force
   sudo php artisan migrate --force
   sudo php artisan db:seed --force
   ```

6. Create an Nginx site configuration:

   ```bash
   sudo nano /etc/nginx/sites-available/school-admin
   ```

   Use this configuration:

   ```nginx
   server {
       listen 80 default_server;
       listen [::]:80 default_server;
       server_name _;
       root /var/www/school-admin/public;
       index index.php index.html;
       charset utf-8;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           include snippets/fastcgi-php.conf;
           fastcgi_pass unix:/run/php/php8.3-fpm.sock;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

   Enable the site and verify the Nginx configuration before reloading:

   ```bash
   sudo ln -s /etc/nginx/sites-available/school-admin /etc/nginx/sites-enabled/school-admin
   sudo rm -f /etc/nginx/sites-enabled/default
   sudo nginx -t
   sudo systemctl reload nginx
   ```

   Open `http://<EC2-public-IPv4>` to check the demo. The seeded accounts use public sample passwords; this demo is public to anyone who can reach the instance and must never contain real data.
7. Check AWS Free plan credits and account status regularly. Stop or terminate the instance and remove any resources you no longer need before the Free plan ends; stopping an instance may not stop charges for attached storage or other resources if you later move to a Paid plan. Do not rely on this demo for backups or availability.

## Manual install
1. `composer create-project laravel/laravel laravel-admin`
2. Copy `app`, `database`, `resources`, `routes` into it.
3. Set `SESSION_DRIVER=file`, `CACHE_STORE=file`, `SESSION_COOKIE=school_admin_session` in `.env`, then `php artisan key:generate && php artisan migrate --seed`.
