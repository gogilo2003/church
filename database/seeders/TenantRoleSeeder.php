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
                'title' => 'Pastor / Senior Leader',
                'name' => 'pastor',
                'description' => 'Full access to members, spiritual growth, attendance, pastoral care, and communication.',
                'permissions' => json_encode([
                    'members.view', 'members.create', 'members.edit',
                    'attendance.view', 'attendance.mark',
                    'sms.send', 'organization.view',
                    'accounts.view',
                ]),
            ],
            [
                'title' => 'Finance Officer / Treasurer',
                'name' => 'finance_officer',
                'description' => 'Access to tithes, offerings, contributions, payment tracking, and financial statements.',
                'permissions' => json_encode([
                    'accounts.view', 'accounts.create', 'accounts.edit',
                    'tithes.record', 'offerings.record', 'contributions.record',
                    'members.view',
                ]),
            ],
            [
                'title' => 'Church Secretary / Administrator',
                'name' => 'secretary',
                'description' => 'Access to member directory, attendance entry, SMS announcements, and general admin tasks.',
                'permissions' => json_encode([
                    'members.view', 'members.create', 'members.edit',
                    'attendance.view', 'attendance.mark',
                    'sms.send',
                ]),
            ],
            [
                'title' => 'Department / Ministry Leader',
                'name' => 'department_leader',
                'description' => 'Access to department member lists and department service attendance.',
                'permissions' => json_encode([
                    'members.view',
                    'attendance.view', 'attendance.mark',
                ]),
            ],
            [
                'title' => 'Financial Auditor',
                'name' => 'auditor',
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
