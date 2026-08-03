<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Household;
use App\Models\Member;
use App\Repositories\Contracts\HouseholdRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class HouseholdService
{
    public function __construct(
        private readonly HouseholdRepositoryInterface $householdRepository
    ) {}

    public function getPaginatedHouseholds(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->householdRepository->getPaginated($filters, $perPage);
    }

    public function findHousehold(int $id): ?Household
    {
        return $this->householdRepository->findById($id);
    }

    public function createHousehold(array $data, array $memberRelationships = []): Household
    {
        return DB::transaction(function () use ($data, $memberRelationships) {
            $household = $this->householdRepository->create($data);

            if (! empty($memberRelationships)) {
                $this->syncMemberRelationships($household, $memberRelationships);
            }

            return $household;
        });
    }

    public function updateHousehold(Household $household, array $data, array $memberRelationships = []): Household
    {
        return DB::transaction(function () use ($household, $data, $memberRelationships) {
            $updated = $this->householdRepository->update($household, $data);

            if (! empty($memberRelationships)) {
                $this->syncMemberRelationships($updated, $memberRelationships);
            }

            return $updated;
        });
    }

    public function deleteHousehold(Household $household): bool
    {
        return $this->householdRepository->delete($household);
    }

    private function syncMemberRelationships(Household $household, array $relationships): void
    {
        $syncData = [];
        foreach ($relationships as $item) {
            if (isset($item['member_id'])) {
                $syncData[$item['member_id']] = [
                    'relationship' => $item['relationship'] ?? 'other',
                ];

                // Also update household_id on member
                Member::where('id', $item['member_id'])->update(['household_id' => $household->id]);
            }
        }

        $household->memberRelationships()->sync($syncData);
    }
}
