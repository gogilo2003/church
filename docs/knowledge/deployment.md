# Deployment & DevOps Pipeline

## Production Infrastructure Requirements
- **PHP**: 8.4 CLI + FPM (`bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`)
- **MySQL**: 8.0+ or MariaDB 10.11+ (Support for dynamic database creation)
- **Node.js**: 20+ LTS
- **Redis**: 7.0+ (Session, Cache, Queue driver)
- **Web Server**: Nginx with wildcard SSL (`*.churchsaas.com`)

## Continuous Deployment Script (GitHub Actions / Laravel Forge)

```bash
#!/bin/bash
set -e

echo "Deploying Application..."

# 1. Pull latest code
git pull origin main

# 2. Install PHP dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# 3. Install NPM dependencies & build frontend assets
npm ci
npm run build

# 4. Run Central Database Migrations
php artisan migrate --force

# 5. Run Tenant Database Migrations
php artisan tenants:migrate --force

# 6. Optimize Laravel Caches
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# 7. Restart Queue Workers
php artisan queue:restart

echo "Deployment finished successfully!"
```

## Multi-Tenant Database Security in Production
- MySQL user for Laravel MUST have `CREATE DATABASE` and `GRANT` privileges to provision new church databases on demand.
- Daily automated backups for Central DB + all active Tenant databases via `spatie/laravel-backup` or cloud database snapshots.
