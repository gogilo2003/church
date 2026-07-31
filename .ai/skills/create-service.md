# Skill Playbook: Create Service

## Rules
- Service classes MUST use `final` class modifier.
- All dependencies injected via constructor property promotion.
- Wrap multi-table updates inside `DB::transaction()`.

## Service Template
```php
<?php

declare(strict_types=1);

namespace App\Services\Tenant;

use App\DataTransferObjects\MemberData;
use App\Events\Tenant\MemberCreated;
use App\Models\Tenant\Member;
use App\Repositories\Contracts\MemberRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class MemberService
{
    public function __construct(
        private readonly MemberRepositoryInterface $memberRepository,
    ) {}

    public function createMember(MemberData $data): Member
    {
        return DB::transaction(function () use ($data) {
            $member = $this->memberRepository->create($data->toArray());

            event(new MemberCreated($member));

            return $member;
        });
    }
}
```
