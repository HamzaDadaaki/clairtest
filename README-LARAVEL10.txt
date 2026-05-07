Afayar website -> Laravel 10 copy pack
=====================================

This pack is prepared from your uploaded Laravel 11 project so you can paste it into a fresh Laravel 10 installation.

WHAT TO COPY INTO YOUR FRESH LARAVEL 10 PROJECT
-----------------------------------------------
1) Copy the folders/files from this pack into your fresh Laravel 10 project root.
2) Allow overwrite when asked.
3) Keep the fresh Laravel 10 versions of these files and DO NOT replace them with the old Laravel 11 ones:
   - bootstrap/app.php
   - public/index.php
   - config/app.php
   - composer.json
   - composer.lock

IMPORTANT LARAVEL 10 CHANGE ALREADY APPLIED HERE
------------------------------------------------
- routes/web.php now uses App\Http\Middleware\VerifyCsrfToken instead of the Laravel 11 middleware import.
- app/Exceptions/Handler.php was added to move the PostTooLargeException handling out of Laravel 11 bootstrap/app.php and into the Laravel 10 exception handler.

OPTIONAL FRONTEND FILES INCLUDED
--------------------------------
- package.json
- vite.config.js
You can copy them too if you want to keep the same Vite/Tailwind setup.

ENV VALUES TO ADD TO .env
-------------------------
ADMIN_EMAIL=admin@afayar.com
ADMIN_PASSWORD=hamza@admin@2026

GOOD TO KNOW
------------
- This site stores catalog, contacts, partners, and analytics in JSON files inside storage/app.
- The JSON files are included in this pack.

AFTER COPYING, RUN THESE COMMANDS
---------------------------------
composer install
php artisan key:generate
php artisan optimize:clear
php artisan storage:link
npm install
npm run build
php artisan serve

IF YOUR APP USES MYSQL INSTEAD OF SQLITE
----------------------------------------
Set DB_* values in .env before running the app.

QUICK CHECKLIST
---------------
[ ] Fresh Laravel 10 project created
[ ] Copied this pack into the project
[ ] Kept fresh Laravel 10 bootstrap/app.php
[ ] Kept fresh Laravel 10 public/index.php
[ ] Added ADMIN_EMAIL and ADMIN_PASSWORD to .env
[ ] Ran composer install / npm install / npm run build
