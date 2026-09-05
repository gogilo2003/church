# Skill Playbook: Create Policy

## Policy Template
```php
<?php

declare(strict_types=1);

namespace App\Policies\Tenant;

use App\Models\Tenant\Member;
use App\Models\Tenant\User;

final class MemberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('members.view');
    }

    public function view(User $user, Member $member): bool
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
