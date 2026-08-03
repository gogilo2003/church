<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Household;
use App\Repositories\Contracts\HouseholdRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class HouseholdRepository implements HouseholdRepositoryInterface
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Household::with(['orgUnit', 'members'])->latest();

        if (! empty($filters['search'])) {
            $search = '%' . strtolower($filters['search']) . '%';
            $query->whereRaw('LOWER(name) LIKE ?', [$search]);
        }

        if (! empty($filters['org_unit_id'])) {
            $query->where('org_unit_id', $filters['org_unit_id']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): ?Household
    {
        return Household::with(['orgUnit', 'members'])->find($id);
    }

    public function create(array $data): Household
    {
        $household = new Household();
        $this->assignProperties($household, $data);
        $household->save();

        return $household;
    }

    public function update(Household $household, array $data): Household
    {
        $this->assignProperties($household, $data);
        $household->save();

        return $household;
    }

    public function delete(Household $household): bool
    {
        return (bool) $household->delete();
    }

    private function assignProperties(Household $household, array $data): void
    {
        if (array_key_exists('org_unit_id', $data)) {
            $household->org_unit_id = $data['org_unit_id'] ? (int) $data['org_unit_id'] : null;
        }
        if (array_key_exists('name', $data)) {
            $household->name = (string) $data['name'];
        }
        if (array_key_exists('primary_contact_phone', $data)) {
            $household->primary_contact_phone = $data['primary_contact_phone'];
        }
        if (array_key_exists('address', $data)) {
            $household->address = $data['address'];
        }
        if (array_key_exists('marriage_date', $data)) {
            $household->marriage_date = $data['marriage_date'];
        }
    }
}
