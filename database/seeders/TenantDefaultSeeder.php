<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\OfferingType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Contracts\Tenant;

final class TenantDefaultSeeder extends Seeder
{
    public function run(): void
    {
        /** @var Tenant|null $tenant */
        $tenant = tenant();

        if ($tenant) {
            $adminEmail = $tenant->data['admin_email'] ?? 'admin@church.test';
            $adminName = $tenant->data['admin_name'] ?? 'Church Administrator';
            $adminPassword = $tenant->data['admin_password'] ?? Hash::make('password');

            // Seed Tenant Admin User in tenant DB
            User::firstOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => $adminName,
                    'password' => $adminPassword,
                    'is_admin' => true,
                    'email_verified_at' => now(),
                ]
            );
        }

        // Seed Default Offering Types
        $defaultOfferingTypes = [
            ['name' => 'Sunday Offering', 'description' => 'General Sunday service collection', 'is_active' => true],
            ['name' => 'Building Fund', 'description' => 'Contributions towards church building & facility projects', 'is_active' => true],
            ['name' => 'Thanksgiving', 'description' => 'Special thanksgiving offerings', 'is_active' => true],
        ];

        foreach ($defaultOfferingTypes as $type) {
            OfferingType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
