# Skill Playbook: Create Controller

## Guidelines
- Controllers MUST NOT perform direct DB logic.
- Must use FormRequests for validation.
- Must use Service layer for operations.

## Controller Template
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreMemberRequest;
use App\Http\Requests\Tenant\UpdateMemberRequest;
use App\Models\Tenant\Member;
use App\Repositories\Contracts\MemberRepositoryInterface;
use App\Services\Tenant\MemberService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

final class MemberController extends Controller
{
    public function __construct(
        private readonly MemberRepositoryInterface $memberRepository,
        private readonly MemberService $memberService,
    ) {}

    public function index(): Response
    {
        $this->authorize('viewAny', Member::class);

        return Inertia::render('Members/Index', [
            'members' => $this->memberRepository->paginate(15),
        ]);
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $this->memberService->createMember($request->toDTO());

        return redirect()->route('members.index')
            ->with('notification', ['success' => 'Member created successfully.']);
    }
}
```
