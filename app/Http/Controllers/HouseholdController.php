<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Household\StoreHouseholdRequest;
use App\Models\Household;
use App\Models\Member;
use App\Models\OrganizationalUnit;
use App\Services\HouseholdService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class HouseholdController extends Controller
{
    public function __construct(
        private readonly HouseholdService $householdService
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'org_unit_id']);
        $households = $this->householdService->getPaginatedHouseholds($filters, 12);
        $members = Member::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'phone']);
        $orgUnits = OrganizationalUnit::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Households/Index', [
            'households' => $households,
            'filters' => $filters,
            'availableMembers' => $members,
            'orgUnits' => $orgUnits,
        ]);
    }

    public function store(StoreHouseholdRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['member_relationships']);
        $relationships = $request->validated('member_relationships') ?? [];

        $this->householdService->createHousehold($data, $relationships);

        return redirect()->route('households.index')
            ->with('notification', ['success' => 'Household created successfully.']);
    }

    public function show(int $id): Response
    {
        $household = $this->householdService->findHousehold($id);
        if (! $household) {
            abort(404, 'Household not found.');
        }

        return Inertia::render('Households/Show', [
            'household' => $household,
        ]);
    }

    public function update(StoreHouseholdRequest $request, int $id): RedirectResponse
    {
        $household = $this->householdService->findHousehold($id);
        if (! $household) {
            abort(404, 'Household not found.');
        }

        $data = $request->safe()->except(['member_relationships']);
        $relationships = $request->validated('member_relationships') ?? [];

        $this->householdService->updateHousehold($household, $data, $relationships);

        return redirect()->route('households.index')
            ->with('notification', ['success' => 'Household updated successfully.']);
    }

    public function destroy(int $id): RedirectResponse
    {
        $household = $this->householdService->findHousehold($id);
        if (! $household) {
            abort(404, 'Household not found.');
        }

        $this->householdService->deleteHousehold($household);

        return redirect()->route('households.index')
            ->with('notification', ['success' => 'Household deleted successfully.']);
    }
}
