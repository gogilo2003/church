# Multi-Tenancy Architecture (stancl/tenancy v3)

## Package Integration
The application uses **stancl/tenancy v3** (`^3.10`) with a **Multi-Database per Tenant** strategy. Each church tenant receives an isolated MySQL database (`church_tenant_{tenant_id}`) and isolated storage disk.

## Tenancy Pipeline & Configuration

### Identification Strategy
1. **Subdomain Identification**: Resolved via `InitializeTenancyByDomainOrSubdomain` (e.g., `ack.church.test`).
2. **Custom Domain Identification**: Custom domains (e.g., `gracecommunity.org`) resolved via the `domains` table linked to tenant ID.

### Tenancy Initialization Steps
When a request hits a tenant route:
1. `InitializeTenancyByDomainOrSubdomain` middleware initializes the tenant context.
2. The package switches Laravel's default database connection `tenant` to point to `church_tenant_{tenant_id}`.
3. Cache prefix is scoped to `tenant_{tenant_id}`.
4. Filesystem disks (`public`, `local`) switch root paths to `storage/tenant_{tenant_id}/`.
5. Tenant configuration (e.g., timezone, currency, branding) is loaded into runtime config.

```php
// config/tenancy.php excerpt
return [
    'tenant_model' => App\Models\Central\Tenant::class,
    'domain_model' => App\Models\Central\Domain::class,
    'central_domains' => array_values(array_unique(array_filter([
        env('CENTRAL_DOMAIN', 'church.test'),
    ]))),
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
- `database/migrations`: Central schema migrations (Tenants, Domains, Central Users, Failed Jobs).
- `database/migrations/tenant`: Tenant-specific migrations (Members, Attendance, Tithes, Offerings, SMS, Roles, Permissions, Organizations).

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
- **`TenantCreated`**: Triggers database creation, schema migration (`tenants:migrate`), default database seeding (roles, permissions, default settings), and welcome notification dispatch.
- **`TenantDeleted`**: Triggers database backup, archive, and optional drop execution after cooling period.
- **`TenancyInitialized`**: Fired on every tenant request initialization.
- **`TenancyEnded`**: Restores central environment database connection and config.

## Tenant Isolation Rules
1. **NO Cross-Database Queries**: Never attempt SQL JOIN queries or cross-database relationships between Central and Tenant databases.
2. **Model Structure**:
   - Central models reside in `App\Models\Central\*` and utilize the central connection.
   - Tenant models reside in `App\Models\*` and execute queries inside the initialized tenant connection.
3. **Queue Jobs**: All tenant-scoped queue jobs MUST implement `Stancl\Tenancy\Contracts\TenantAware` or use the `TenantAware` job wrapper.
4. **Auth & Redirection Separation**:
   - Central Platform Admin login is at `/admin/login` (`central.admin.login`).
   - Central `/login` is an alias route (`central.login`) that redirects to `/admin/login`.
   - Tenant User login is at `/login` on the tenant's subdomain (`{subdomain}.church.test/login`).
