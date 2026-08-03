<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Visitor\StoreVisitorRequest;
use App\Models\User;
use App\Models\OrganizationalUnit;
use App\Services\VisitorFollowUpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class VisitorFollowUpController extends Controller
{
    public function __construct(
        private readonly VisitorFollowUpService $followUpService
    ) {}

    public function index(): Response
    {
        $board = $this->followUpService->getPipelineBoard();
        $leaders = User::orderBy('name')->get(['id', 'name', 'email']);
        $orgUnits = OrganizationalUnit::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Visitors/Index', [
            'board' => $board,
            'leaders' => $leaders,
            'orgUnits' => $orgUnits,
        ]);
    }

    public function store(StoreVisitorRequest $request): RedirectResponse
    {
        $memberData = $request->safe()->only(['first_name', 'middle_name', 'last_name', 'gender', 'phone', 'email', 'org_unit_id']);
        $followUpData = $request->safe()->only(['visit_date', 'visit_purpose', 'prayer_requests', 'notes', 'assigned_user_id']);

        $this->followUpService->createVisitorIntake($memberData, $followUpData);

        return redirect()->route('visitors.index')
            ->with('notification', ['success' => 'Visitor intake registered successfully.']);
    }

    public function updateStage(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'stage' => ['required', 'string', 'in:new,contacted,visited,in_classes,converted,dropped'],
        ]);

        $this->followUpService->updateStage($id, $request->input('stage'));

        return redirect()->back()
            ->with('notification', ['success' => 'Visitor follow-up stage updated.']);
    }

    public function convert(int $id): RedirectResponse
    {
        $this->followUpService->convertVisitorToMember($id);

        return redirect()->route('members.index')
            ->with('notification', ['success' => 'Visitor successfully converted to active church member.']);
    }

    public function assignLeader(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'assigned_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $this->followUpService->assignLeader($id, $request->input('assigned_user_id') ? (int) $request->input('assigned_user_id') : null);

        return redirect()->back()
            ->with('notification', ['success' => 'Follow-up leader assigned.']);
    }
}
