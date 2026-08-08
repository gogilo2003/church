<?php

declare(strict_types=1);

use App\Models\Assignment;
use App\Models\HierarchyDefinition;
use App\Models\HierarchyLevel;
use App\Models\Member;
use App\Models\Offering;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Tithe;
use App\Models\User;
use App\Services\HierarchicalReportService;
use App\Services\HierarchyService;
use App\Services\ScopeAuthorizationService;

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('migrate', [
        '--path' => [
            'database/migrations/tenant/2026_08_01_000001_create_organizations_table.php',
            'database/migrations/tenant/2026_08_01_000002_create_hierarchy_definitions_table.php',
            'database/migrations/tenant/2026_08_01_000003_create_hierarchy_levels_table.php',
            'database/migrations/tenant/2026_08_01_000004_create_organizational_units_table.php',
            'database/migrations/tenant/2026_08_01_000005_create_org_unit_closures_table.php',
            'database/migrations/tenant/2026_08_01_000006_create_institutions_table.php',
            'database/migrations/tenant/2026_08_01_000007_create_projects_table.php',
            'database/migrations/tenant/2026_08_01_000008_create_assignments_table.php',
            'database/migrations/tenant/2026_08_01_000009_add_org_unit_id_to_domain_tables.php',
            'database/migrations/tenant/2023_07_24_061459_create_members_table.php',
            'database/migrations/tenant/2023_09_11_054852_create_attendances_table.php',
            'database/migrations/tenant/2024_03_09_050413_create_tithes_table.php',
            'database/migrations/tenant/2024_03_09_050519_create_offerings_table.php',
        ],
    ]);
});

test('it creates denominational hierarchy and populates closure tree correctly', function () {
    $hierarchyService = app(HierarchyService::class);

    // 1. Create Organization & Hierarchy Definition for ELCK
    $organization = Organization::create([
        'name' => 'Evangelical Lutheran Church in Kenya',
        'code' => 'ELCK',
    ]);

    $definition = HierarchyDefinition::create([
        'organization_id' => $organization->id,
        'name' => 'Diocesan Structure',
        'is_active' => true,
    ]);

    $dioceseLevel = HierarchyLevel::create([
        'hierarchy_definition_id' => $definition->id,
        'depth' => 1,
        'name' => 'Diocese',
        'plural_name' => 'Dioceses',
    ]);

    $parishLevel = HierarchyLevel::create([
        'hierarchy_definition_id' => $definition->id,
        'depth' => 2,
        'name' => 'Parish',
        'plural_name' => 'Parishes',
    ]);

    $congregationLevel = HierarchyLevel::create([
        'hierarchy_definition_id' => $definition->id,
        'depth' => 3,
        'name' => 'Congregation',
        'plural_name' => 'Congregations',
    ]);

    // 2. Create Units in Tree
    $nairobiDiocese = $hierarchyService->createUnit([
        'organization_id' => $organization->id,
        'hierarchy_level_id' => $dioceseLevel->id,
        'parent_id' => null,
        'name' => 'Nairobi Diocese',
    ]);

    $cathedralParish = $hierarchyService->createUnit([
        'organization_id' => $organization->id,
        'hierarchy_level_id' => $parishLevel->id,
        'parent_id' => $nairobiDiocese->id,
        'name' => 'Cathedral Parish',
    ]);

    $stMarkCongregation = $hierarchyService->createUnit([
        'organization_id' => $organization->id,
        'hierarchy_level_id' => $congregationLevel->id,
        'parent_id' => $cathedralParish->id,
        'name' => 'St. Mark Congregation',
    ]);

    $kisumuDiocese = $hierarchyService->createUnit([
        'organization_id' => $organization->id,
        'hierarchy_level_id' => $dioceseLevel->id,
        'parent_id' => null,
        'name' => 'Kisumu Diocese',
    ]);

    // 3. Verify Closure Descendants
    $nairobiDescendants = $hierarchyService->getDescendantIds($nairobiDiocese->id);
    expect($nairobiDescendants)->toContain($nairobiDiocese->id, $cathedralParish->id, $stMarkCongregation->id)
        ->and($nairobiDescendants)->not()->toContain($kisumuDiocese->id);
});

test('it inherits scope permissions down the organizational tree', function () {
    $hierarchyService = app(HierarchyService::class);
    $scopeAuthService = app(ScopeAuthorizationService::class);

    $organization = Organization::create(['name' => 'ELCK', 'code' => 'ELCK']);
    $definition = HierarchyDefinition::create(['organization_id' => $organization->id, 'name' => 'Main', 'is_active' => true]);
    $dioceseLevel = HierarchyLevel::create(['hierarchy_definition_id' => $definition->id, 'depth' => 1, 'name' => 'Diocese', 'plural_name' => 'Dioceses']);
    $parishLevel = HierarchyLevel::create(['hierarchy_definition_id' => $definition->id, 'depth' => 2, 'name' => 'Parish', 'plural_name' => 'Parishes']);

    $nairobiDiocese = $hierarchyService->createUnit(['organization_id' => $organization->id, 'hierarchy_level_id' => $dioceseLevel->id, 'name' => 'Nairobi Diocese']);
    $cathedralParish = $hierarchyService->createUnit(['organization_id' => $organization->id, 'hierarchy_level_id' => $parishLevel->id, 'parent_id' => $nairobiDiocese->id, 'name' => 'Cathedral Parish']);
    $kisumuDiocese = $hierarchyService->createUnit(['organization_id' => $organization->id, 'hierarchy_level_id' => $dioceseLevel->id, 'name' => 'Kisumu Diocese']);

    $user = User::factory()->create(['is_admin' => false]);
    $role = Role::create(['title' => 'Finance Secretary', 'permissions' => '[]']);

    // Assign role scoped to Nairobi Diocese
    Assignment::create([
        'user_id' => $user->id,
        'role_id' => $role->id,
        'organizational_unit_id' => $nairobiDiocese->id,
    ]);

    // Nairobi Diocese Admin should automatically have scope access over Cathedral Parish
    expect($scopeAuthService->hasScopeAccess($user, $nairobiDiocese->id))->toBeTrue()
        ->and($scopeAuthService->hasScopeAccess($user, $cathedralParish->id))->toBeTrue()
        ->and($scopeAuthService->hasScopeAccess($user, $kisumuDiocese->id))->toBeFalse();
});

test('it aggregates financial and membership rollups across descendant units', function () {
    $hierarchyService = app(HierarchyService::class);
    $reportService = app(HierarchicalReportService::class);

    $organization = Organization::create(['name' => 'ELCK', 'code' => 'ELCK']);
    $definition = HierarchyDefinition::create(['organization_id' => $organization->id, 'name' => 'Main', 'is_active' => true]);
    $dioceseLevel = HierarchyLevel::create(['hierarchy_definition_id' => $definition->id, 'depth' => 1, 'name' => 'Diocese', 'plural_name' => 'Dioceses']);
    $parishLevel = HierarchyLevel::create(['hierarchy_definition_id' => $definition->id, 'depth' => 2, 'name' => 'Parish', 'plural_name' => 'Parishes']);
    $congregationLevel = HierarchyLevel::create(['hierarchy_definition_id' => $definition->id, 'depth' => 3, 'name' => 'Congregation', 'plural_name' => 'Congregations']);

    $nairobiDiocese = $hierarchyService->createUnit(['organization_id' => $organization->id, 'hierarchy_level_id' => $dioceseLevel->id, 'name' => 'Nairobi Diocese']);
    $cathedralParish = $hierarchyService->createUnit(['organization_id' => $organization->id, 'hierarchy_level_id' => $parishLevel->id, 'parent_id' => $nairobiDiocese->id, 'name' => 'Cathedral Parish']);
    $stMarkCongregation = $hierarchyService->createUnit(['organization_id' => $organization->id, 'hierarchy_level_id' => $congregationLevel->id, 'parent_id' => $cathedralParish->id, 'name' => 'St. Mark Congregation']);

    $user = User::factory()->create();

    // Create records under different nodes
    Tithe::create(['user_id' => $user->id, 'org_unit_id' => $nairobiDiocese->id, 'tithed_on' => '2026-07-30', 'amount' => 5000]);
    Tithe::create(['user_id' => $user->id, 'org_unit_id' => $stMarkCongregation->id, 'tithed_on' => '2026-07-30', 'amount' => 15000]);

    Offering::create(['user_id' => $user->id, 'org_unit_id' => $cathedralParish->id, 'offering_date' => '2026-07-30', 'amount' => 10000]);

    Member::create(['org_unit_id' => $stMarkCongregation->id, 'first_name' => 'John', 'last_name' => 'Doe', 'gender' => 'male', 'phone' => '0712345678', 'date_of_birth' => '1990-01-01']);

    $summary = $reportService->getSummaryForUnit($nairobiDiocese->id);

    expect((float) $summary['total_tithes'])->toBe(20000.0)
        ->and((float) $summary['total_offerings'])->toBe(10000.0)
        ->and($summary['total_members'])->toBe(1);
});
