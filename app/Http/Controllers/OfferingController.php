<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfferingRequest;
use App\Http\Requests\UpdateOfferingRequest;
use App\Models\Offering;
use App\Models\OfferingType;
use Inertia\Inertia;

class OfferingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->input('search');

        $offerings = Offering::paginate(8)->through(fn(Offering $offering) => [
            "id" => $offering->id,
            "offering_date" => $offering->offering_date->isoFormat('ddd D MMM, Y'),
            "amount" => $offering->amount,
            "type" => $offering->type ? [
                "id" => $offering->type->id,
                "name" => $offering->type->name,
            ] : null,
            "user" => [
                "id" => $offering->user->id,
                "name" => $offering->user->name,
            ],
        ]);

        $types = OfferingType::all()->map(fn(OfferingType $offeringType) => [
            "value" => $offeringType->id,
            "text" => $offeringType->name,
        ]);
        return Inertia::render('Offerings/Index', [
            'offerings' => $offerings,
            'types' => $types,
            'search' => $search
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOfferingRequest $request)
    {
        $offering = new Offering();
        $offering->offering_date = $request->offering_date;
        $offering->amount = $request->amount;
        $offering->offering_type_id = $request->type;
        $offering->user_id = $request->user()->id;
        $offering->save();

        return redirect()->back()->with('success', 'Offering stored');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfferingRequest $request, Offering $offering)
    {
        $offering->offering_date = $request->offering_date;
        $offering->amount = $request->amount;
        $offering->offering_type_id = $request->type;
        $offering->user_id = $request->user()->id;
        $offering->save();

        return redirect()->back()->with('success', 'Offering updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offering $offering)
    {
        $offering->delete();

        return redirect()->back()->with('success', 'Offering deleted');
    }
}
