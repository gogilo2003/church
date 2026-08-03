<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MemberRepositoryInterface
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Member;

    public function create(array $data): Member;

    public function update(Member $member, array $data): Member;

    public function delete(Member $member): bool;

    public function updateStatus(Member $member, string $newStatus): Member;
}
