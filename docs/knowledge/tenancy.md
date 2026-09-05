# Multi-Tenancy Architecture (stancl/tenancy v4)

## Package Integration
The application uses **stancl/tenancy v4** with a **Multi-Database per Tenant** strategy. Each church tenant receives an isolated MySQL database (`church_tenant_{tenant_id}`) and isolated storage disk.

## Tenancy Pipeline & Configuration

### Identification Strategy
1. **Subdomain Identification**: `TenantResolver` extracts the subdomain (e.g., `grace.churchsaas.com`).
2. **Domain Identification**: Custom domain lookup (e.g., `gracecommunity.org`) resolved via `domains` database table linked to tenant ID.

### Tenancy Initialization Steps
When a request hits a tenant route:
1. `InitializeTenancyByDomain` or `InitializeTenancyBySubdomain` middleware triggers.
2. The package switches Laravel's `db` connection `tenant` to point to `church_tenant_{tenant_id}`.
3. Cache prefix is scoped to `tenant_{tenant_id}`.
4. Filesystem disks (`public`, `local`) switch root paths to `storage/tenant_{tenant_id}/`.
5. Tenant configuration (e.g., timezone, currency, branding) is loaded into runtime config.

```php
// config/tenancy.php excerpt
return [
    'tenant_model' => App\Models\Central\Tenant::class,
    'domain_model' => App\Models\Central\Domain::class,
    'central_domains' => [
        'churchsaas.com',
        'app.churchsaas.com',
        'localhost',
    ],
    'bootstrappers' => [
        Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
    ],
];
```

## Database Migrations
Migrations are strictly segregated into two folders:
- `database/migrations`: Central schema migrations (Tenants, Domains, Subscriptions, Central Users).
- `database/migrations/tenant`: Tenant-specific migrations (Members, Attendance, Tithes, Offerings, SMS, Roles, Permissions, Audit Logs).

### Running Migrations
```bash
# Run central migrations
php artisan migrate

# Run tenant migrations across all tenant databases
php artisan tenants:migrate

# Seed all tenant databases
php artisan tenants:seed
```

## Tenant Lifecycle Events
- **`TenantCreated`**: Triggers database creation, schema migration (`tenants:migrate`), default database seeding (roles, permissions, default settings), and welcome email dispatch.
- **`TenantDeleted`**: Triggers database backup, archive, and optional drop execution after cooling period.
- **`TenancyInitialized`**: Fired on every tenant request initialization.
- **`TenancyEnded`**: Restores central environment database connection and config.

## Tenant Isolation Rules
1. **NO Cross-Database Queries**: Never attempt SQL JOIN queries between Central and Tenant databases.
2. **Model Base Classes**:
   - Central models extend `App\Models\Central\BaseCentralModel`.
   - Tenant models extend `App\Models\Tenant\BaseTenantModel`.
3. **Queue Jobs**: All tenant-scoped queue jobs MUST use the `TenantAware` trait or inherit `Stancl\Tenancy\Queue\TenantAwareJob`.
