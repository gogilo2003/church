# Skill Playbook: Create Repository

## Contract Interface (`app/Repositories/Contracts/MemberRepositoryInterface.php`)
```php
<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Tenant\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MemberRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Member;
    public function create(array $data): Member;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
```

## Eloquent Implementation (`app/Repositories/Eloquent/MemberRepository.php`)
```php
<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Tenant\Member;
use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class MemberRepository implements MemberRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Member::latest()->paginate($perPage);
    }

    public function findById(int $id): ?Member
    {
        return Member::find($id);
    }

    public function create(array $data): Member
    {
        return Member::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $member = $this->findById($id);
        return $member ? $member->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $member = $this->findById($id);
        return $member ? (bool) $member->delete() : false;
    }
}
```
