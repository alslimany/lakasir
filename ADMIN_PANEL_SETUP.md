# Admin Panel Setup for Production

## Overview
The admin panel is now accessible on the central domain at `/admin` path instead of requiring a separate subdomain.

## Access URL
- **Production**: `https://kashir.ly/admin`
- **Local Development**: `http://localhost/admin`

## Configuration

### 1. Configure Environment Variables

In your production `.env` file, set your central domain:

```env
APP_CENTRAL_DOMAIN=kashir.ly
APP_URL=https://kashir.ly
```

### 2. Configure DNS

Make sure your DNS has an A record pointing to your server:
```
kashir.ly  →  Your Server IP
```

### 3. Configure Web Server

#### Nginx Configuration

Your existing Nginx configuration for the central domain will handle admin routes:

```nginx
server {
    listen 80;
    server_name kashir.ly;
    root /var/www/lakasir/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Then enable SSL with Let's Encrypt:
```bash
sudo certbot --nginx -d kashir.ly
```

#### Apache Configuration

Your existing Apache configuration for the central domain will handle admin routes:

```apache
<VirtualHost *:80>
    ServerName kashir.ly
    DocumentRoot /var/www/lakasir/public

    <Directory /var/www/lakasir/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/kashir.ly-error.log
    CustomLog ${APACHE_LOG_DIR}/kashir.ly-access.log combined
</VirtualHost>
```

Then enable SSL with Let's Encrypt:
```bash
sudo certbot --apache -d kashir.ly
```

### 4. Clear Cache

After configuration, clear the cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 5. Verify Configuration

Check that routes are properly registered:
```bash
php artisan route:list --path=admin
```

You should see Filament admin routes listed.

## How It Works

The admin panel is now configured to be accessible on the central domain:

1. Admin panel routes are available at `/admin` path
2. The `InitializeTenancyByDomain` middleware skips tenancy initialization for `/admin` routes
3. No separate subdomain configuration needed
4. Works the same in local development and production

## Local Development

For local development, the admin panel works without domain configuration:
- Access at: `http://localhost/admin`
- Or: `http://your-local-domain.test/admin`

## Production Access

After proper configuration:
- Admin Panel: `https://kashir.ly/admin`
- Tenant Registration: `https://kashir.ly/auth/register`
- Tenant Domains: `https://tenant1.kashir.ly`

## Troubleshooting

### 404 Error on Admin Panel

1. **Check Environment Variables**:
   ```bash
   php artisan tinker
   >>> env('APP_CENTRAL_DOMAIN')
   >>> config('tenancy.central_domains')
   ```

2. **Check Routes**:
   ```bash
   php artisan route:list --path=admin
   ```

3. **Check Filament Panel**:
   ```bash
   php artisan filament:list-panels
   ```

4. **Clear Cache**:
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan cache:clear
   ```

### Redirect Loop

If you experience redirect loops:
1. Check SSL configuration
2. Ensure `APP_URL` in `.env` uses `https://`
3. Configure `TRUSTED_PROXIES` if behind a load balancer

### 500 Error

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check web server logs
3. Verify file permissions: `storage` and `bootstrap/cache` should be writable

## Related Documentation

- [MULTITENANCY_SAAS.md](MULTITENANCY_SAAS.md) - Complete system documentation
- [README_MULTITENANCY.md](README_MULTITENANCY.md) - Quick start guide

## Support

If you continue to have issues:
1. Check Laravel logs
2. Verify environment configuration
3. Ensure DNS is properly configured
4. Check web server configuration
