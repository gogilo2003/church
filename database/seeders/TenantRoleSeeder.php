<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class TenantRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'title' => 'Pastor',
                'name' => 'pastor',
                'display_name' => 'Pastor / Senior Leader',
                'description' => 'Full access to members, spiritual growth, attendance, pastoral care, and communication.',
                'permissions' => json_encode([
                    'members.view', 'members.create', 'members.edit',
                    'attendance.view', 'attendance.mark',
                    'sms.send', 'organization.view',
                    'accounts.view',
                ]),
            ],
            [
                'title' => 'Finance Officer',
                'name' => 'finance_officer',
                'display_name' => 'Finance Officer / Treasurer',
                'description' => 'Access to tithes, offerings, contributions, payment tracking, and financial statements.',
                'permissions' => json_encode([
                    'accounts.view', 'accounts.create', 'accounts.edit',
                    'tithes.record', 'offerings.record', 'contributions.record',
                    'members.view',
                ]),
            ],
            [
                'title' => 'Secretary',
                'name' => 'secretary',
                'display_name' => 'Church Secretary / Administrator',
                'description' => 'Access to member directory, attendance entry, SMS announcements, and general admin tasks.',
                'permissions' => json_encode([
                    'members.view', 'members.create', 'members.edit',
                    'attendance.view', 'attendance.mark',
                    'sms.send',
                ]),
            ],
            [
                'title' => 'Department Leader',
                'name' => 'department_leader',
                'display_name' => 'Department / Ministry Leader',
                'description' => 'Access to department member lists and department service attendance.',
                'permissions' => json_encode([
                    'members.view',
                    'attendance.view', 'attendance.mark',
                ]),
            ],
            [
                'title' => 'Auditor',
                'name' => 'auditor',
                'display_name' => 'Financial Auditor',
                'description' => 'Read-only access to financial ledgers, tithes, offerings, and compliance records.',
                'permissions' => json_encode([
                    'accounts.view', 'tithes.view', 'offerings.view', 'contributions.view',
                ]),
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['title' => $roleData['title']],
                $roleData
            );
        }
    }
}
