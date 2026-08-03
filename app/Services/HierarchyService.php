<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrganizationalUnit;
use App\Models\OrgUnitClosure;
use Illuminate\Support\Facades\DB;

final class HierarchyService
{
    /**
     * Create an OrganizationalUnit and automatically maintain closure table paths.
     */
    public function createUnit(array $data): OrganizationalUnit
    {
        return DB::transaction(function () use ($data) {
            $unit = OrganizationalUnit::create($data);

            // Self-referential closure entry (depth = 0)
            OrgUnitClosure::create([
                'ancestor_id' => $unit->id,
                'descendant_id' => $unit->id,
                'depth' => 0,
            ]);

            // If a parent is present, copy all parent's ancestors with depth + 1
            if ($unit->parent_id) {
                $ancestors = OrgUnitClosure::where('descendant_id', $unit->parent_id)->get();
                foreach ($ancestors as $ancestor) {
                    OrgUnitClosure::create([
                        'ancestor_id' => $ancestor->ancestor_id,
                        'descendant_id' => $unit->id,
                        'depth' => $ancestor->depth + 1,
                    ]);
                }
            }

            return $unit;
        });
    }

    /**
     * Get all descendant IDs for a given OrganizationalUnit (including itself).
     */
    public function getDescendantIds(int $unitId): array
    {
        return OrgUnitClosure::where('ancestor_id', $unitId)
            ->pluck('descendant_id')
            ->toArray();
    }

    /**
     * Get all ancestor IDs for a given OrganizationalUnit (including itself).
     */
    public function getAncestorIds(int $unitId): array
    {
        return OrgUnitClosure::where('descendant_id', $unitId)
            ->pluck('ancestor_id')
            ->toArray();
    }
}
