<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TenantUserRepositoryInterface
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getRoles(): Collection;

    public function create(array $data): User;

    public function update(int $userId, array $data): User;

    public function updateStatus(int $userId, string $status): User;

    public function resetPassword(int $userId, string $hashedPassword): User;
}
