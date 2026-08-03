<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OrgUnitClosure;
use App\Models\User;

final class ScopeAuthorizationService
{
    /**
     * Check if a user has authorization scope over a target OrganizationalUnit.
     */
    public function hasScopeAccess(User $user, int $targetOrgUnitId): bool
    {
        // Central Admins have global access over all units
        if ($user->is_admin) {
            return true;
        }

        // Get all OrgUnits where the user holds an active role assignment
        $assignedUnitIds = $user->assignments()->pluck('organizational_unit_id')->toArray();

        if (empty($assignedUnitIds)) {
            return false;
        }

        // Exact match
        if (in_array($targetOrgUnitId, $assignedUnitIds, true)) {
            return true;
        }

        // Ancestor check via closure table: Is any assigned unit an ancestor of targetOrgUnitId?
        return OrgUnitClosure::whereIn('ancestor_id', $assignedUnitIds)
            ->where('descendant_id', $targetOrgUnitId)
            ->exists();
    }
}
