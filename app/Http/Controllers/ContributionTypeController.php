<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Contribution;
use App\Models\ContributionType;
use App\Http\Resources\ContributionTypeResource;
use App\Http\Requests\StoreContributionTypeRequest;
use App\Http\Requests\UpdateContributionTypeRequest;

class ContributionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contribution_types = ContributionTypeResource::collection(ContributionType::paginate());
        return Inertia::render('Contributions/Index', ['contribution_types' => $contribution_types]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContributionTypeRequest $request)
    {
        $type = new ContributionType();
        $type->description = $request->description;
        $type->recurrent = $request->recurrent;
        $type->recurrence_value = $request->recurrence_value;
        $type->recurrence_unit = $request->recurrence_unit;
        $type->back_date = $request->back_date;
        $type->deadline = $request->deadline;
        $type->amount = $request->amount;
        $type->save();

        return redirect()->back()->with('success', 'Contribution type has been created');
    }

    /**
     * Display the specified resource.
     */
    public function show(ContributionType $contribution)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContributionType $contribution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContributionTypeRequest $request, ContributionType $contribution)
    {
        $type->description = $request->description;
        $type->recurrent = $request->recurrent;
        $type->recurrence_value = $request->recurrence_value;
        $type->recurrence_unit = $request->recurrence_unit;
        $type->back_date = $request->back_date;
        $type->deadline = $request->deadline;
        $type->amount = $request->amount;
        $type->save();

        return redirect()->back()->with('success', 'Contribution type has been created');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContributionType $contribution)
    {
        //
    }
}
