<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContributionRequest;
use App\Http\Requests\UpdateContributionRequest;
use App\Models\Contribution;
use App\Models\ContributionType;
use Illuminate\Support\Carbon;

class ContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContributionRequest $request, ContributionType $contribution_type)
    {

        $contribution = new Contribution();
        $contribution->contribution_type_id = $request->contribution_type;
        $contribution->member_id = $request->member;
        $contribution->amount = $request->amount ? $request->amount : null;
        $contribution->end_at = Carbon::parse($request->end_at);
        $contribution->save();

        $contribution->load('contribution_type');

        return redirect()->back()->with('success', 'Member registered for ' . $contribution->contribution_type->description);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contribution $contribution)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contribution $contribution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContributionRequest $request, Contribution $contribution)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contribution $contribution)
    {
        //
    }
}
