# Role-Based Access Control (RBAC) & Authorization

## Roles Hierarchy (Tenant Scope)

```
              ┌───────────────────────────┐
              │     Church Super Admin    │
              └─────────────┬─────────────┘
                            │
              ┌─────────────┴─────────────┐
              │                           │
    ┌─────────▼─────────┐       ┌─────────▼─────────┐
    │   Pastor / Leader │       │ Finance Manager   │
    └─────────┬─────────┘       └─────────┬─────────┘
              │                           │
    ┌─────────▼─────────┐       ┌─────────▼─────────┐
    │   Ministry Staff  │       │  Data Collector   │
    └───────────────────┘       └───────────────────┘
```

## Permission Matrix

| Module | Super Admin | Pastor / Leader | Finance Manager | Ministry Staff |
| :--- | :---: | :---: | :---: | :---: |
| **Members (View/Create/Edit)** | Full | Full | View Only | View / Edit |
| **Members (Delete)** | Full | Denied | Denied | Denied |
| **Tithes & Offerings** | Full | View Summary | Full | Denied |
| **Attendance (Mark/Edit)** | Full | Full | View Only | Full |
| **SMS Messaging Center** | Full | Full | Denied | Send Only |
| **Settings & Branding** | Full | Denied | Denied | Denied |

## Implementation Standard

### Spatie Laravel-Permission Integration
Roles and Permissions exist inside each tenant database schema.

### Policy Method Pattern
```php
declare(strict_types=1);

namespace App\Policies\Tenant;

use App\Models\Tenant\User;
use App\Models\Tenant\Member;

final class MemberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('members.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('members.create');
    }

    public function update(User $user, Member $member): bool
    {
        return $user->hasPermissionTo('members.edit');
    }

    public function delete(User $user, Member $member): bool
    {
        return $user->hasPermissionTo('members.delete');
    }
}
```
