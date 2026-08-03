<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\HierarchyDefinition;
use App\Models\HierarchyLevel;
use App\Models\Organization;
use App\Models\OrganizationalUnit;
use App\Services\HierarchyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller
{
    public function __construct(
        protected HierarchyService $hierarchyService
    ) {}

    public function index(): Response
    {
        $organization = Organization::firstOrCreate(
            ['code' => 'MAIN'],
            ['name' => 'Main Organization', 'description' => 'Default Church Structure']
        );

        $definition = HierarchyDefinition::firstOrCreate(
            ['organization_id' => $organization->id, 'is_active' => true],
            ['name' => 'Standard Church Hierarchy']
        );

        $levels = HierarchyLevel::where('hierarchy_definition_id', $definition->id)
            ->orderBy('depth')
            ->get();

        $units = OrganizationalUnit::with(['level', 'parent', 'children'])
            ->where('organization_id', $organization->id)
            ->get();

        return Inertia::render('Organization/Index', [
            'organization' => $organization,
            'definition' => $definition,
            'levels' => $levels,
            'units' => $units,
        ]);
    }

    public function storeLevel(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hierarchy_definition_id' => 'required|exists:hierarchy_definitions,id',
            'name' => 'required|string|max:255',
            'plural_name' => 'required|string|max:255',
            'depth' => 'required|integer|min:0',
            'allow_institutions' => 'boolean',
        ]);

        HierarchyLevel::create($validated);

        return back()->with('success', 'Hierarchy level created successfully.');
    }

    public function storeUnit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'hierarchy_level_id' => 'required|exists:hierarchy_levels,id',
            'parent_id' => 'nullable|exists:organizational_units,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        $this->hierarchyService->createUnit($validated);

        return back()->with('success', 'Organizational unit created successfully.');
    }

    public function destroyUnit(OrganizationalUnit $unit): RedirectResponse
    {
        $unit->delete();

        return back()->with('success', 'Organizational unit removed.');
    }
}
