# ADMIN_PANEL_SETUP.md — Explanation & Copilot Guidance

Purpose
- Explains how to configure and deploy the central admin panel on the central domain (e.g., `kashir.ly/admin`).

Key points
- Central domain configuration uses `APP_CENTRAL_DOMAIN` and `APP_URL` in `.env`.
- Admin guard and `App\Models\Admin` are separate from tenant users.
- Nginx/Apache configuration snippets present in the file.
- Important post-deploy steps: run migrations, seed admin user, clear caches, enable SSL (certbot).

Copilot tasks
- When asked to help set up production admin panel, reference this file for DNS, web server, and cache commands.
- If making changes to routing or middleware to exclude `/admin` from tenancy, update docs here.

Verification
- Verify `php artisan route:list --path=admin` shows Filament routes.
- Ensure `admin` guard is configured in `config/auth.php` and `admins` table exists.