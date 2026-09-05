# Security Architecture & Policies

## Tenant Data Isolation

### 1. Hard Isolation via Multi-Database
Each tenant's data resides in a physically separate MySQL database (`church_tenant_{tenant_id}`). This eliminates cross-tenant data leakage via SQL queries.

### 2. Middleware Enforcement
Tenant routes MUST be wrapped inside the `tenancy` middleware group:
```php
Route::middleware([
    'web',
    'initialize_tenancy',
])->group(function () {
    // All tenant routes
});
```

### 3. File Isolation
Files uploaded by a tenant (e.g. member photos, tithe receipts) are stored in isolated storage directories: `storage/app/tenants/{tenant_id}/`.

## Authentication & Authorization
- **Authentication**: Laravel Fortify / Breeze Inertia authentication stack.
- **Tenant User Isolation**: Tenant authentication evaluates users inside the active tenant's DB only. Central super admins login via the central domain.
- **Password Security**: Hashed using Argon2id or Bcrypt with minimum cost 12.
- **Session Security**: Session cookies are scoped to the host domain (`.churchsaas.com` or custom domain).

## Attack Defenses
- **CSRF Protection**: Enabled on all POST/PUT/DELETE requests via Inertia `X-XSRF-TOKEN`.
- **SQL Injection**: Exclusively use Eloquent ORM & PDO parameterized queries. Avoid raw SQL queries.
- **XSS Prevention**: Automatic Vue template escaping + HTML sanitization on free text inputs.
- **Rate Limiting**: Enforce `ThrottleRequests` on authentication endpoints (5 attempts per minute) and SMS dispatches.
