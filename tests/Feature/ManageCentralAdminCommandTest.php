<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('artisan central:admin list displays admin users', function () {
    User::factory()->create([
        'name' => 'System Super Admin',
        'email' => 'superadmin@church.test',
        'is_admin' => true,
    ]);

    $this->artisan('central:admin', ['action' => 'list'])
        ->expectsTable(
            ['ID', 'Name', 'Email', 'Created At'],
            [
                ['1', 'System Super Admin', 'superadmin@church.test', now()->toDateTimeString()],
            ]
        )
        ->assertExitCode(0);
});

test('artisan central:admin promote upgrades user to admin', function () {
    $user = User::factory()->create([
        'email' => 'member@church.test',
        'is_admin' => false,
    ]);

    $this->artisan('central:admin', [
        'action' => 'promote',
        '--email' => 'member@church.test',
    ])->assertExitCode(0);

    expect($user->fresh()->is_admin)->toBeTrue();
});

test('artisan central:admin demote downgrades admin user', function () {
    User::factory()->create(['email' => 'admin1@church.test', 'is_admin' => true]);
    $admin2 = User::factory()->create(['email' => 'admin2@church.test', 'is_admin' => true]);

    $this->artisan('central:admin', [
        'action' => 'demote',
        '--email' => 'admin2@church.test',
    ])->assertExitCode(0);

    expect($admin2->fresh()->is_admin)->toBeFalse();
});
