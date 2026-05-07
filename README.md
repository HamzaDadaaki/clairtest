# Clara Studio Laravel 10 Demo

A polished Laravel 10 / PHP 8.2 storefront demo for Claire's art business.

## Included pages
- Home
- Arts / products listing
- Single product page with gallery + inquiry form
- About me
- Contact
- Hidden admin panel

## Admin login
- URL: `/admin/login`
- Default password: `clara-demo-2026`
- Change it in `.env`:

```env
CLARA_ADMIN_PASSWORD=your-new-password
```

## What the admin panel can manage
- Add / edit / delete products
- View contact messages
- View purchase inquiries

## Demo storage
This package stores products, contact messages, and purchase inquiries in:
- `storage/app/demo/products.json`
- `storage/app/demo/messages.json`
- `storage/app/demo/orders.json`

For production, replace this with a full database-backed implementation using migrations, authentication, mail notifications, and image uploads.

## Setup
1. Create a fresh Laravel 10 project.
2. Copy these files into it.
3. Put the `public/assets` folder in your Laravel `public` directory.
4. Add the routes and controllers.
5. Run:

```bash
php artisan optimize:clear
php artisan storage:link
```

## Notes
- This is a strong visual demo and working prototype.
- It does **not** yet include payment gateways, real review moderation, image upload handling, user accounts, or database migrations.
- It is designed so you can show Claire a polished demo first, then extend it into a full production store.
