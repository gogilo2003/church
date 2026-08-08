<?php

declare(strict_types=1);

use App\Models\Central\Tenant;
use App\Models\HierarchyLevel;
use App\Models\OrganizationalUnit;
use App\Models\OfferingType;
use App\Models\RevenueSharingRule;
use App\Models\VoteHead;
use App\Services\DenominationalPresetService;
use App\Services\RevenueSharingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('denominational preset service returns valid presets and seeds levels & vote heads', function () {
    $presetService = app(DenominationalPresetService::class);
    $presets = $presetService->getPresets();

    expect($presets)->toHaveKeys(['lutheran', 'anglican', 'independent']);
});

test('revenue sharing service calculates split simulation correctly for tithe collection', function () {
    // 1. Create Tenant context & basic tables
    $this->artisan('migrate', [
        '--path' => [
            'database/migrations/tenant/2026_08_01_000001_create_organizations_table.php',
            'database/migrations/tenant/2026_08_01_000002_create_hierarchy_definitions_table.php',
            'database/migrations/tenant/2026_08_01_000003_create_hierarchy_levels_table.php',
            'database/migrations/tenant/2026_08_01_000004_create_organizational_units_table.php',
            'database/migrations/tenant/2026_08_01_000005_create_org_unit_closures_table.php',
            'database/migrations/tenant/2026_08_08_000030_create_vote_heads_table.php',
            'database/migrations/tenant/2026_08_08_000031_create_revenue_sharing_tables.php',
        ],
    ]);

    $organization = \App\Models\Organization::create(['name' => 'ELCK', 'code' => 'ELCK']);
    $definition = \App\Models\HierarchyDefinition::create(['organization_id' => $organization->id, 'name' => 'Main', 'is_active' => true]);

    $dioceseLevel = HierarchyLevel::create(['hierarchy_definition_id' => $definition->id, 'depth' => 1, 'name' => 'Diocese', 'plural_name' => 'Dioceses']);
    $parishLevel = HierarchyLevel::create(['hierarchy_definition_id' => $definition->id, 'depth' => 2, 'name' => 'Parish', 'plural_name' => 'Parishes']);

    $diocese = OrganizationalUnit::create(['organization_id' => $organization->id, 'hierarchy_level_id' => $dioceseLevel->id, 'name' => 'Nairobi Diocese']);
    $parish = OrganizationalUnit::create(['organization_id' => $organization->id, 'hierarchy_level_id' => $parishLevel->id, 'parent_id' => $diocese->id, 'name' => 'Cathedral Parish']);

    // Create Rule: 15% Tithe upward from Parish to Diocese
    RevenueSharingRule::create([
        'name' => 'Parish to Diocese Tithe Split (15%)',
        'source_type' => 'tithe',
        'source_scope_type' => 'global',
        'destination_type' => 'parent',
        'direction' => 'upward',
        'priority' => 1,
        'calculation_type' => 'percentage',
        'value' => 15.0,
    ]);

    $sharingService = app(RevenueSharingService::class);
    $splits = $sharingService->simulate('tithe', null, $parish->id, 10000.00);

    expect($splits)->toHaveCount(1);
    expect($splits[0]['distributed_amount'])->toBe(1500.00);
    expect($splits[0]['destination_unit']['name'])->toBe('Nairobi Diocese');
});
