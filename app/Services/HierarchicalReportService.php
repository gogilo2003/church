<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\Offering;
use App\Models\Tithe;

final class HierarchicalReportService
{
    public function __construct(
        private readonly HierarchyService $hierarchyService,
    ) {}

    /**
     * Compute aggregated financial & membership totals for an OrgUnit and its descendants.
     */
    public function getSummaryForUnit(int $unitId): array
    {
        $descendantIds = $this->hierarchyService->getDescendantIds($unitId);

        $totalTithes = Tithe::whereIn('org_unit_id', $descendantIds)->sum('amount');
        $totalOfferings = Offering::whereIn('org_unit_id', $descendantIds)->sum('amount');
        $totalMembers = Member::whereIn('org_unit_id', $descendantIds)->count();
        $totalAttendances = Attendance::whereIn('org_unit_id', $descendantIds)->count();

        return [
            'unit_id' => $unitId,
            'descendants_count' => count($descendantIds),
            'total_tithes' => $totalTithes,
            'total_offerings' => $totalOfferings,
            'total_members' => $totalMembers,
            'total_attendances' => $totalAttendances,
        ];
    }
}
