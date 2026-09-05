# Role-Based Access Control (RBAC) & Authorization Architecture

## Architecture Overview

The system operates a **Dual-Level RBAC & Scope Model**:

1. **Central SaaS Platform Level**: Controls access for Central Platform Staff (`/admin/*` domain routes).
   - Roles: `super_admin`, `support_agent`, `finance_manager`, `onboarding_agent`.
   - Evaluated via `EnsureCentralUser` middleware and `$user->isSuperAdmin()` / `$user->hasCentralPermission(...)`.
2. **Tenant Church Workspace Level**: Controls access for local church staff within isolated tenant subdomains (`/dashboard`, `/members`, `/accounts`, etc.).
   - Evaluated via `$user->is_admin` (Tenant Super-User bypass) or `$user->hasPermission(...)`.

---

## User & Role Schema (Many-to-Many)

```
┌──────────────┐         ┌───────────┐         ┌──────────────┐
│    User      │ 1     * │ role_user │ *     1 │    Role      │
├──────────────┤─────────┼───────────┼─────────├──────────────┤
│ id           │         │ user_id   │         │ id           │
│ name         │         │ role_id   │         │ title        │
│ email        │         └───────────┘         │ name         │
│ is_admin     │                               │ permissions  │ (JSON array)
│ role         │                               └──────────────┘
└──────────────┘
```

---

## Permission Aggregation (`$user->permissions`)

The `User` model aggregates permissions from all assigned roles into a combined array accessible via `$user->permissions`:

```php
// Backend Model Accessor (App\Models\User)
public function permissions(): Attribute
{
    return Attribute::get(function () {
        if ($this->is_admin) {
            return ['*']; // Wildcard full access for admins
        }

        return $this->roles
            ->pluck('permissions')
            ->filter()
            ->map(fn ($p) => is_string($p) ? json_decode($p, true) : $p)
            ->flatten()
            ->unique()
            ->values()
            ->all();
    });
}
```

---

## Backend Authorization Standard

In Controllers, Middleware, or Policies:

```php
// Check specific granular permission (Admin automatically bypasses with true)
if (! $request->user()->hasPermission('members.create')) {
    abort(403, 'Unauthorized module access');
}
```

---

## Frontend Authorization Standard (Vue 3 + Inertia)

Because `permissions` is appended to `User` array serialization (`$appends = ['permissions']`), `$page.props.auth.user.permissions` is automatically exposed to all Vue components:

```vue
<template>
    <!-- Wildcard or specific permission check -->
    <button 
        v-if="$page.props.auth.user.permissions.includes('*') || $page.props.auth.user.permissions.includes('members.create')"
        @click="openCreateModal"
    >
        + Add New Member
    </button>
</template>
```

