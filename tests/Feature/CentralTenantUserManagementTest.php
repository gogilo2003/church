<?php

declare(strict_types=1);

use App\Models\Central\Tenant;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\CentralRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    static $sharedTenant = null;

    $this->seed(CentralRoleSeeder::class);

    $this->admin = User::factory()->create([
        'is_admin' => true,
        'email' => 'ops@platform.test',
    ]);

    // Provision a single tenant once per process and reuse it across tests.
    // RefreshDatabase only resets the central DB, so the tenant persists.
    if ($sharedTenant === null) {
        $sharedTenant = Tenant::create([
            'id' => 'grace'.time(),
            'church_name' => 'Grace Chapel',
            'admin_email' => 'pastor@gracechapel.org',
            'admin_name' => 'Pastor John',
        ]);

        $sharedTenant->domains()->create([
            'domain' => $sharedTenant->id,
            'is_primary' => true,
        ]);
    }

    $this->tenant = $sharedTenant;
});

test('central admin can view a tenant user list page', function () {
    $tenantId = $this->tenant->id;

    $this->actingAs($this->admin)
        ->get(route('central.admin.tenants.users.index', $tenantId))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Central/Admin/Tenants/Users')
            ->has('roles.data')
            ->where('tenant.id', $tenantId));
});

test('central admin can create, update, suspend and reset a tenant user password', function () {
    $tenantId = $this->tenant->id;

    $roleId = $this->tenant->run(fn () => Role::where('name', 'secretary')->value('id'));

    $this->actingAs($this->admin)
        ->post(route('central.admin.tenants.users.store', $tenantId), [
            'name' => 'Jane Doe',
            'email' => 'jane@gracechapel.org',
            'username' => 'jdoe',
            'phone_number' => '+254712345679',
            'status' => 'active',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role_ids' => [$roleId],
        ])
        ->assertRedirect();

    $this->tenant->run(function () use ($roleId) {
        expect(User::where('email', 'jane@gracechapel.org')->exists())->toBeTrue();

        expect(User::where('email', 'jane@gracechapel.org')->firstOrFail()->roles->pluck('id'))
            ->toContain($roleId);
    });

    $userId = $this->tenant->run(fn () => User::where('email', 'jane@gracechapel.org')->value('id'));

    $this->actingAs($this->admin)
        ->patch(route('central.admin.tenants.users.toggle-status', [
            'tenant' => $tenantId,
            'user' => $userId,
        ]), ['status' => 'suspended'])
        ->assertRedirect();

    $this->tenant->run(fn () => expect(User::find($userId)->status)->toBe('suspended'));

    $this->actingAs($this->admin)
        ->post(route('central.admin.tenants.users.reset-password', [
            'tenant' => $tenantId,
            'user' => $userId,
        ]), [
            'password' => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ])
        ->assertRedirect();

    $this->tenant->run(
        fn () => expect(Hash::check('NewPassword456!', User::find($userId)->password))->toBeTrue()
    );

    $this->actingAs($this->admin)
        ->patch(route('central.admin.tenants.users.update', [
            'tenant' => $tenantId,
            'user' => $userId,
        ]), [
            'name' => 'Jane Doe Jr.',
            'email' => 'jane@gracechapel.org',
            'username' => 'jdoe',
            'phone_number' => '+254712345679',
            'status' => 'active',
        ])
        ->assertRedirect();

    $this->tenant->run(fn () => expect(User::find($userId)->name)->toBe('Jane Doe Jr.'));
});

test('central admin cannot create duplicate tenant user emails', function () {
    $tenantId = $this->tenant->id;

    $this->actingAs($this->admin)
        ->post(route('central.admin.tenants.users.store', $tenantId), [
            'name' => 'Pastor John',
            'email' => 'pastor@gracechapel.org',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])
        ->assertSessionHasErrors('email');

    $this->tenant->run(
        fn () => expect(User::where('email', 'pastor@gracechapel.org')->count())->toBe(1)
    );
});

test('duplicate emails are allowed across different tenants', function () {
    $otherTenant = Tenant::create([
        'id' => 'hopefellowship'.time(),
        'church_name' => 'Hope Fellowship',
        'admin_email' => 'admin@hopefellowship.org',
    ]);

    $otherTenant->domains()->create([
        'domain' => $otherTenant->id,
        'is_primary' => true,
    ]);

    $this->actingAs($this->admin)
        ->post(route('central.admin.tenants.users.store', $otherTenant->id), [
            'name' => 'Pastor John',
            'email' => 'pastor@gracechapel.org',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])
        ->assertRedirect();

    $this->tenant->run(
        fn () => expect(User::where('email', 'pastor@gracechapel.org')->count())->toBe(1)
    );
});
