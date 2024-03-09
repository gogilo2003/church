<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfferingRequest;
use App\Http\Requests\UpdateOfferingRequest;
use App\Models\Offering;

class OfferingController extends Controller
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
    public function store(StoreOfferingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Offering $offering)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offering $offering)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfferingRequest $request, Offering $offering)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offering $offering)
    {
        //
    }
}
