<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Stancl\Tenancy\Events\TenantCreated;

uses(RefreshDatabase::class);

test('guest can view tenant self-registration page', function () {
    $response = $this->get(route('central.register-tenant.create'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page->component('Central/Auth/RegisterTenant'));
});

test('subdomain availability API validates alphanumeric requirement and reserved words', function () {
    // Valid available subdomain
    $response = $this->getJson(route('central.check-subdomain', ['subdomain' => 'grace']));
    $response->assertOk()
        ->assertJson(['available' => true]);

    // Invalid non-alphanumeric subdomain
    $response = $this->getJson(route('central.check-subdomain', ['subdomain' => 'grace-church']));
    $response->assertOk()
        ->assertJson(['available' => false]);

    // Reserved subdomain
    $response = $this->getJson(route('central.check-subdomain', ['subdomain' => 'admin']));
    $response->assertOk()
        ->assertJson(['available' => false]);
});

test('church admin can register new tenant with alphanumeric subdomain', function () {
    Event::fake([TenantCreated::class]);

    $payload = [
        'church_name' => 'Grace Chapel',
        'subdomain' => 'gracechapel',
        'admin_name' => 'Pastor John',
        'admin_email' => 'john@gracechapel.org',
        'admin_password' => 'Password123!',
        'admin_password_confirmation' => 'Password123!',
        'phone' => '+254712345678',
    ];

    $response = $this->post(route('central.register-tenant.store'), $payload);

    $this->assertDatabaseHas('tenants', [
        'id' => 'gracechapel',
    ]);

    $this->assertDatabaseHas('domains', [
        'tenant_id' => 'gracechapel',
        'domain' => 'gracechapel',
        'is_primary' => true,
    ]);

    Event::assertDispatched(TenantCreated::class);
});
