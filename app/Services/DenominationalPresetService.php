<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HierarchyDefinition;
use App\Models\HierarchyLevel;
use App\Models\OfferingType;
use App\Models\RevenueSharingRule;
use App\Models\VoteHead;

final class DenominationalPresetService
{
    public function getPresets(): array
    {
        return [
            'lutheran' => [
                'name' => 'Evangelical Lutheran Church Polity',
                'description' => 'Structure with General Secretariat (Synod) -> Diocese -> District -> Parish -> Congregation',
                'levels' => [
                    ['depth' => 1, 'name' => 'General Secretariat', 'plural_name' => 'General Secretariats'],
                    ['depth' => 2, 'name' => 'Diocese', 'plural_name' => 'Dioceses'],
                    ['depth' => 3, 'name' => 'District / Deanery', 'plural_name' => 'Districts'],
                    ['depth' => 4, 'name' => 'Parish', 'plural_name' => 'Parishes'],
                    ['depth' => 5, 'name' => 'Congregation', 'plural_name' => 'Congregations'],
                ],
                'vote_heads' => [
                    ['code' => '4010', 'name' => 'Tithe Collections', 'type' => 'income'],
                    ['code' => '4020', 'name' => 'Sunday Service Offering', 'type' => 'income'],
                    ['code' => '4030', 'name' => 'Building & Capital Fund', 'type' => 'income'],
                    ['code' => '4040', 'name' => 'Thanksgiving Collection', 'type' => 'income'],
                    ['code' => '5010', 'name' => 'Pastoral & Clergy Support', 'type' => 'expense'],
                    ['code' => '5020', 'name' => 'Utilities & Facility Operations', 'type' => 'expense'],
                    ['code' => '5030', 'name' => 'Diocesan & Synodical Quota Remittance', 'type' => 'expense'],
                ],
                'default_rules' => [
                    [
                        'name' => 'Parish to Diocese Tithe Remittance (15%)',
                        'source_type' => 'tithe',
                        'source_scope_type' => 'global',
                        'destination_type' => 'parent',
                        'direction' => 'upward',
                        'priority' => 1,
                        'calculation_type' => 'percentage',
                        'value' => 15.0,
                    ],
                ],
            ],
            'anglican' => [
                'name' => 'Anglican / Episcopal Polity',
                'description' => 'Structure with Province -> Diocese -> Archdeaconry -> Parish',
                'levels' => [
                    ['depth' => 1, 'name' => 'Provincial Head Office', 'plural_name' => 'Provincial Offices'],
                    ['depth' => 2, 'name' => 'Diocese', 'plural_name' => 'Dioceses'],
                    ['depth' => 3, 'name' => 'Archdeaconry', 'plural_name' => 'Archdeaconries'],
                    ['depth' => 4, 'name' => 'Parish', 'plural_name' => 'Parishes'],
                ],
                'vote_heads' => [
                    ['code' => '4010', 'name' => 'Assessment & Tithes', 'type' => 'income'],
                    ['code' => '4020', 'name' => 'Offertory Collections', 'type' => 'income'],
                    ['code' => '5010', 'name' => 'Stipends & Salaries', 'type' => 'expense'],
                    ['code' => '5020', 'name' => 'Diocesan Assessment Remittance', 'type' => 'expense'],
                ],
                'default_rules' => [
                    [
                        'name' => 'Diocesan Assessment Split (10%)',
                        'source_type' => 'tithe',
                        'source_scope_type' => 'global',
                        'destination_type' => 'parent',
                        'direction' => 'upward',
                        'priority' => 1,
                        'calculation_type' => 'percentage',
                        'value' => 10.0,
                    ],
                ],
            ],
            'independent' => [
                'name' => 'Independent / Single Campus Church',
                'description' => 'Simple structure with Main Ministry Headquarters -> Local Campus / Department',
                'levels' => [
                    ['depth' => 1, 'name' => 'Main Ministry Centre', 'plural_name' => 'Ministry Centres'],
                    ['depth' => 2, 'name' => 'Branch Campus / Department', 'plural_name' => 'Branch Campuses'],
                ],
                'vote_heads' => [
                    ['code' => '4010', 'name' => 'Tithes Received', 'type' => 'income'],
                    ['code' => '4020', 'name' => 'General Sunday Offering', 'type' => 'income'],
                    ['code' => '5010', 'name' => 'Operational Expenses', 'type' => 'expense'],
                ],
                'default_rules' => [],
            ],
        ];
    }

    public function applyPreset(string $presetKey, int $organizationId): bool
    {
        $presets = $this->getPresets();

        if (! isset($presets[$presetKey])) {
            return false;
        }

        $preset = $presets[$presetKey];

        // 1. Create Hierarchy Definition & Levels
        $definition = HierarchyDefinition::create([
            'organization_id' => $organizationId,
            'name' => $preset['name'],
            'is_active' => true,
        ]);

        foreach ($preset['levels'] as $levelData) {
            HierarchyLevel::create([
                'hierarchy_definition_id' => $definition->id,
                'depth' => $levelData['depth'],
                'name' => $levelData['name'],
                'plural_name' => $levelData['plural_name'],
            ]);
        }

        // 2. Seed Vote Heads
        foreach ($preset['vote_heads'] as $vhData) {
            VoteHead::firstOrCreate(
                ['code' => $vhData['code']],
                [
                    'name' => $vhData['name'],
                    'type' => $vhData['type'],
                    'is_active' => true,
                ]
            );
        }

        // 3. Seed Default Revenue Rules
        foreach ($preset['default_rules'] as $ruleData) {
            RevenueSharingRule::create($ruleData);
        }

        return true;
    }
}
