<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTitheRequest;
use App\Http\Requests\UpdateTitheRequest;
use App\Models\Tithe;
use Inertia\Inertia;

class TitheController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->input('search');

        $tithes = Tithe::when($search, function ($query) use ($search) {
            $query->where('tithed_on', $search);
        })->paginate(8)
            ->through(fn(Tithe $tithe) => [
                "id" => $tithe->id,
                "tithed_on" => $tithe->tithed_on,
                "amount" => $tithe->amount,
                "user" => [
                    "id" => $tithe->user->id,
                    "name" => $tithe->user->name,
                ],
            ]);

        return Inertia::render('Tithes/Index', ['tithes' => $tithes, 'search' => $search]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTitheRequest $request)
    {
        $tithe = new Tithe();
        $tithe->tithed_on = $request->tithed_on;
        $tithe->amount = $request->amount;
        $tithe->user_id = $request->user()->id;
        $tithe->save();

        return redirect()->back()->with('success', 'Tithe stored');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTitheRequest $request, Tithe $tithe)
    {
        $tithe->tithed_on = $request->tithed_on;
        $tithe->amount = $request->amount;
        $tithe->user_id = $request->user()->id;
        $tithe->save();

        return redirect()->back()->with('success', 'Tithe updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tithe $tithe)
    {
        $tithe->delete();

        return redirect()->back()->with('success', 'Tithe deleted');
    }
}
