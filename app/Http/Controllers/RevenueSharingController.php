<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\HierarchyLevel;
use App\Models\OfferingType;
use App\Models\OrganizationalUnit;
use App\Models\Project;
use App\Models\RevenueDistribution;
use App\Models\RevenueSharingRule;
use App\Models\VoteHead;
use App\Services\RevenueSharingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class RevenueSharingController extends Controller
{
    public function __construct(
        private readonly RevenueSharingService $sharingService,
    ) {}

    public function index(): Response
    {
        return Inertia::render('RevenueSharing/Index', [
            'rules' => RevenueSharingRule::with(['sourceScopeLevel', 'sourceScopeUnit', 'destinationUnit', 'destinationLevel', 'offeringType', 'project'])->orderBy('priority')->get(),
            'distributions' => RevenueDistribution::with(['rule', 'sourceUnit', 'destinationUnit'])->latest()->take(50)->get(),
            'hierarchyLevels' => HierarchyLevel::orderBy('depth')->get(),
            'orgUnits' => OrganizationalUnit::orderBy('name')->get(),
            'offeringTypes' => OfferingType::where('is_active', true)->get(),
            'projects' => Project::all(),
            'voteHeads' => VoteHead::where('is_active', true)->get(),
        ]);
    }

    public function storeRule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'source_type' => 'required|in:tithe,offering_type,project',
            'source_id' => 'nullable|integer',
            'source_scope_type' => 'required|in:global,hierarchy_level,org_unit',
            'source_scope_id' => 'nullable|integer',
            'destination_type' => 'required|in:parent,ancestor_at_level,specific_unit',
            'destination_id' => 'nullable|integer',
            'direction' => 'required|in:upward,downward,direct',
            'priority' => 'required|integer|min:0',
            'calculation_type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'min_collection_threshold' => 'nullable|numeric|min:0',
            'max_cap_amount' => 'nullable|numeric|min:0',
        ]);

        RevenueSharingRule::create($validated);

        return redirect()->back()->with('success', 'Revenue sharing rule created successfully.');
    }

    public function simulate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'source_type' => 'required|in:tithe,offering_type,project',
            'source_id' => 'nullable|integer',
            'source_unit_id' => 'required|integer',
            'gross_amount' => 'required|numeric|min:0.01',
        ]);

        $splits = $this->sharingService->simulate(
            $validated['source_type'],
            $validated['source_id'] ?? null,
            (int) $validated['source_unit_id'],
            (float) $validated['gross_amount']
        );

        return response()->json(['splits' => $splits]);
    }
}
