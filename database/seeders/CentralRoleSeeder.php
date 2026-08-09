<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CentralRoleSeeder extends Seeder
{
    /**
     * Seed default Central Platform roles and an initial super admin.
     */
    public function run(): void
    {
        $roles = [
            [
                'title' => 'Super Admin',
                'name' => 'super_admin',
                'description' => 'Full control over the entire SaaS platform.',
                'is_system_role' => true,
                'permissions' => ['*'],
            ],
            [
                'title' => 'Support Agent',
                'name' => 'support_agent',
                'description' => 'Can view tenants, assist with onboarding, and manage support tickets.',
                'is_system_role' => true,
                'permissions' => ['tenants.view', 'tenants.support', 'help.manage'],
            ],
            [
                'title' => 'Finance Manager',
                'name' => 'finance_manager',
                'description' => 'Manages SaaS subscriptions, billing, and financial reports.',
                'is_system_role' => true,
                'permissions' => ['billing.view', 'billing.manage', 'subscriptions.view', 'subscriptions.manage'],
            ],
            [
                'title' => 'Onboarding Agent',
                'name' => 'onboarding_agent',
                'description' => 'Handles new church registration and initial setup.',
                'is_system_role' => true,
                'permissions' => ['tenants.create', 'tenants.view', 'onboarding.manage'],
            ],
        ];

        foreach ($roles as $roleData) {
            \App\Models\Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}
