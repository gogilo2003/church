<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Household;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface HouseholdRepositoryInterface
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Household;

    public function create(array $data): Household;

    public function update(Household $household, array $data): Household;

    public function delete(Household $household): bool;
}
