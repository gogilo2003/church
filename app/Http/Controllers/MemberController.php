<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Models\Member;
use App\Models\OrganizationalUnit;
use App\Models\Household;
use App\Services\MemberService;
use App\Services\HouseholdService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class MemberController extends Controller
{
    public function __construct(
        private readonly MemberService $memberService,
        private readonly HouseholdService $householdService
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'status', 'org_unit_id']);
        $members = $this->memberService->getPaginatedMembers($filters, 12);
        $orgUnits = OrganizationalUnit::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Members/Index', [
            'members' => $members,
            'filters' => $filters,
            'orgUnits' => $orgUnits,
        ]);
    }

    public function create(): Response
    {
        $orgUnits = OrganizationalUnit::orderBy('name')->get(['id', 'name']);
        $households = Household::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Members/Create', [
            'orgUnits' => $orgUnits,
            'households' => $households,
        ]);
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $member = $this->memberService->createMember($request->validated());

        return redirect()->route('members.show', $member->id)
            ->with('notification', ['success' => 'Member registered successfully.']);
    }

    public function show(int $id): Response
    {
        $member = $this->memberService->findMember($id);
        if (! $member) {
            abort(404, 'Member not found.');
        }

        return Inertia::render('Members/Show', [
            'member' => $member,
        ]);
    }

    public function edit(int $id): Response
    {
        $member = $this->memberService->findMember($id);
        if (! $member) {
            abort(404, 'Member not found.');
        }

        $orgUnits = OrganizationalUnit::orderBy('name')->get(['id', 'name']);
        $households = Household::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Members/Edit', [
            'member' => $member,
            'orgUnits' => $orgUnits,
            'households' => $households,
        ]);
    }

    public function update(UpdateMemberRequest $request, int $id): RedirectResponse
    {
        $member = $this->memberService->findMember($id);
        if (! $member) {
            abort(404, 'Member not found.');
        }

        $this->memberService->updateMember($member, $request->validated());

        return redirect()->route('members.show', $member->id)
            ->with('notification', ['success' => 'Member details updated successfully.']);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:visitor,new_convert,new_member,active,inactive,transferred,suspended,deceased'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $member = $this->memberService->findMember($id);
        if (! $member) {
            abort(404, 'Member not found.');
        }

        $this->memberService->changeMemberStatus($member, $request->input('status'), $request->input('reason'));

        return redirect()->back()
            ->with('notification', ['success' => 'Member status updated successfully.']);
    }

    public function destroy(int $id): RedirectResponse
    {
        $member = $this->memberService->findMember($id);
        if (! $member) {
            abort(404, 'Member not found.');
        }

        $this->memberService->deleteMember($member);

        return redirect()->route('members.index')
            ->with('notification', ['success' => 'Member deleted successfully.']);
    }
}
