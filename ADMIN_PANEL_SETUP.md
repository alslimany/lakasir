# Admin Panel Setup for Production

## Issue
When deploying to production, the admin panel at `https://admin.domain.com/admin` returns a 404 error.

## Root Cause
The admin panel needs proper domain configuration in production to work correctly. Filament panels need to know which domains they should respond to.

## Solution

### 1. Configure Environment Variables

In your production `.env` file, set either:

**Option A: Set APP_ADMIN_DOMAIN directly**
```env
APP_ADMIN_DOMAIN=admin.kashir.ly
APP_CENTRAL_DOMAIN=kashir.ly
```

**Option B: Set only APP_CENTRAL_DOMAIN (admin domain will be auto-constructed)**
```env
APP_CENTRAL_DOMAIN=kashir.ly
# Admin domain will automatically be: admin.kashir.ly
```

### 2. Configure DNS

Make sure your DNS has an A record or CNAME pointing to your server:
```
admin.kashir.ly  →  Your Server IP
```

### 3. Configure Web Server

#### Nginx Configuration

Add a server block for the admin domain:

```nginx
server {
    listen 80;
    server_name admin.kashir.ly;
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
sudo certbot --nginx -d admin.kashir.ly
```

#### Apache Configuration

Add a virtual host for the admin domain:

```apache
<VirtualHost *:80>
    ServerName admin.kashir.ly
    DocumentRoot /var/www/lakasir/public

    <Directory /var/www/lakasir/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/admin.kashir.ly-error.log
    CustomLog ${APACHE_LOG_DIR}/admin.kashir.ly-access.log combined
</VirtualHost>
```

Then enable SSL with Let's Encrypt:
```bash
sudo certbot --apache -d admin.kashir.ly
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

The `AdminPanelProvider` now automatically:

1. Checks for `APP_ADMIN_DOMAIN` environment variable
2. If not set, constructs admin domain from `APP_CENTRAL_DOMAIN`
3. Configures Filament panel to respond to that domain
4. Skips domain configuration for local development

## Local Development

For local development, the admin panel works without domain configuration:
- Access at: `http://localhost/admin`
- Or: `http://your-local-domain.test/admin`

## Production Access

After proper configuration:
- Admin Panel: `https://admin.kashir.ly/admin`
- Tenant Registration: `https://kashir.ly/auth/register`
- Tenant Domains: `https://tenant1.kashir.ly`

## Troubleshooting

### 404 Error on Admin Domain

1. **Check DNS**: Ensure `admin.kashir.ly` resolves to your server IP
   ```bash
   dig admin.kashir.ly
   # or
   nslookup admin.kashir.ly
   ```

2. **Check Environment Variables**:
   ```bash
   php artisan tinker
   >>> env('APP_ADMIN_DOMAIN')
   >>> env('APP_CENTRAL_DOMAIN')
   >>> config('tenancy.admin_domains')
   ```

3. **Check Web Server**: Ensure virtual host is configured and enabled

4. **Check Filament Panel**:
   ```bash
   php artisan filament:list-panels
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
