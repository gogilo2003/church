<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfferingTypeRequest;
use App\Http\Requests\UpdateOfferingTypeRequest;
use App\Models\OfferingType;

class OfferingTypeController extends Controller
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
    public function store(StoreOfferingTypeRequest $request)
    {
        $offeringType = new OfferingType();

        $offeringType->name = $request->name;

        $offeringType->save();

        return redirect()->back()->with('success', 'Offering type stored');
    }

    /**
     * Display the specified resource.
     */
    public function show(OfferingType $offeringType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OfferingType $offeringType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfferingTypeRequest $request, OfferingType $offeringType)
    {
        $offeringType->name = $request->name;

        $offeringType->save();

        return redirect()->back()->with('success', 'Offering type updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OfferingType $offeringType)
    {
        //
    }
}
