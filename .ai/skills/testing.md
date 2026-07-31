# Skill Playbook: Write Pest Tests

## Tenant Feature Test Template
```php
<?php

declare(strict_types=1);

use App\Models\Tenant\Member;
use App\Models\Tenant\User;

beforeEach(function () {
    $this->tenant = createTenant();
    tenancy()->initialize($this->tenant);
    $this->user = User::factory()->create();
});

test('authenticated user can view members list', function () {
    Member::factory()->count(3)->create();

    $response = $this->actingAs($this->user)->get(route('members.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page->component('Members/Index'));
});
```
