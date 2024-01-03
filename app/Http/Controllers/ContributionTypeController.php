<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use NumberFormatter;
use App\Models\Member;
use App\Models\Contribution;
use Illuminate\Support\Carbon;
use App\Models\ContributionType;
use App\Http\Resources\ContributionTypeResource;
use App\Http\Requests\StoreContributionTypeRequest;
use App\Http\Requests\UpdateContributionTypeRequest;
use App\Models\Payment;

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
        $type->autoenroll = $request->autoenroll;
        $type->deadline = $request->deadline ? Carbon::parse($request->deadline) : null;
        $type->amount = $request->amount > 0 ? $request->amount : null;
        $type->save();

        return redirect()->back()->with('success', 'Contribution type has been created');
    }

    /**
     * Display the specified resource.
     */
    public function show(ContributionType $contribution_type)
    {
        $contribution_type;

        $members = Member::with(['contributions.payments', 'contributions' => function ($query) use ($contribution_type) {
            $query->whereHas('contribution_type', function ($query) use ($contribution_type) {
                $query->where('contribution_type_id', $contribution_type->id)->orderBy('end_at', 'DESC');
            });
        }])->paginate(5)->through(function ($member) use ($contribution_type) {
            $contributions = $member->contributions;

            $contributions = $contributions->map(function ($contribution) use ($contribution_type) {
                $description = "";
                $deadline = $contribution_type->deadline ? Carbon::parse($contribution_type->deadline) : null;

                if ($contribution_type->recurrent) {

                    if ($contribution->end_at) {
                        $deadline = $contribution->end_at;
                    } else {
                        if ($contribution_type->recurrence_unit == 'day') {
                            $deadline = $contribution->created_at;
                        } elseif ($contribution_type->recurrence_unit == 'week') {
                            $deadline = $contribution_type->created_at->addWeek($contribution_type->recurrence_value);
                        } elseif ($contribution_type->recurrence_unit == 'month') {
                            $deadline = $contribution_type->created_at->addMonth($contribution_type->recurrence_value);
                        } elseif ($contribution_type->recurrence_unit == 'year') {
                            $deadline = $contribution_type->created_at->addYear($contribution_type->recurrence_value);
                        }
                    }

                    $description = $this->contributionDescription($deadline, $contribution_type);
                }

                $paid = $contribution->payments->sum('amount');

                // return $contribution;

                return (object)[
                    "id" => $contribution->id,
                    "contribution_date" => $contribution->contribution_date,
                    "end_at" => $deadline,
                    "description" => $description,
                    "amount" => $contribution->amount ?? 0,
                    "paid" => $paid,
                    "balance" => $contribution->amount - $paid,
                    "status" => $contribution->contribution_status,
                ];
            });

            $contributions_max = $this->contributionsMax($contribution_type);

            // if ($contributions->count() < $contributions_max) {
            $allContributions = $this->getAllContributions($contributions, $contribution_type);
            $contributions = $allContributions;
            // }
            // dump($contributions->count(), $contributions_max, $contributions);
            $contributions = $contributions->sortByDesc('description', SORT_NATURAL)->values();

            return (object)[
                "id" => $member->id,
                "name" => trim(sprintf("%s %s", $member->first_name, $member->last_name)),
                "photo" => $member->photo_url,
                "phone" => $member->phone,
                "email" => $member->email,
                "gender" => $member->gender ? 'Male' : 'Female',
                "contributions_count" => $member->contributions->count(),
                "contributions_max" => $contributions_max,
                "contributions" => $contributions,
            ];
        });

        return Inertia::render('Contributions/Show', [
            'contribution_type' => ContributionTypeResource::make($contribution_type),
            'members' => $members,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContributionType $contribution_type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContributionTypeRequest $request, ContributionType $contribution_type)
    {
        $contribution_type->description = $request->description;
        $contribution_type->recurrent = $request->recurrent;
        $contribution_type->recurrence_value = $request->recurrence_value;
        $contribution_type->recurrence_unit = $request->recurrence_unit;
        $contribution_type->back_date = $request->back_date;
        $contribution_type->autoenroll = $request->autoenroll;
        $contribution_type->deadline = $request->deadline ? Carbon::parse($request->deadline) : null;
        $contribution_type->amount = $request->amount > 0 ? $request->amount : null;
        $contribution_type->save();

        return redirect()->back()->with('success', 'Contribution type has been updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContributionType $contribution_type)
    {
        //
    }

    protected function contributionsMax($contribution_type)
    {
        $count = 1;

        if ($contribution_type->recurrent) {
            if ($contribution_type->recurrence_unit == 'day') {
                $count = $contribution_type->created_at->diffInDays(now());
            } elseif ($contribution_type->recurrence_unit == 'week') {
                $count = $contribution_type->created_at->diffInWeeks(now());
            } elseif ($contribution_type->recurrence_unit == 'month') {
                $count = $contribution_type->created_at->diffInMonths(now());
            } elseif ($contribution_type->recurrence_unit == 'year') {
                $count = $contribution_type->created_at->diffInYears(now());
            }
            ++$count;
        }
        return $count;
    }

    protected function contributionDescription($end_date, $contribution_type)
    {
        if ($contribution_type->recurrence_unit == 'day') {
            $locale = 'en_KE';
            $nf = new NumberFormatter($locale, NumberFormatter::ORDINAL);
            $days_count = $contribution_type->created_at->diffInDays($end_date);
            return sprintf('%s day', $nf->format($days_count));
        }
        if ($contribution_type->recurrence_unit == 'week') {
            return sprintf('week %s', $end_date->format('w'));
        }
        if ($contribution_type->recurrence_unit == 'month') {
            return sprintf('Month %s', $end_date->format('M-Y'));
        }
        if ($contribution_type->recurrence_unit == 'year') {
            return sprintf('%s', $end_date->format('Y'));
        }
    }

    public function getAllContributions($contributions, $contribution_type)
    {
        $all = collect(range(1, $this->contributionsMax($contribution_type)))->map(function ($item) use ($contribution_type, $contributions) {

            $end_date = Carbon::parse($contribution_type->created_at);

            if ($contribution_type->recurrence_unit == 'day') {
                $end_date->addDays($item);
            }
            if ($contribution_type->recurrence_unit == 'week') {
                $end_date->addWeeks($item);
            }
            if ($contribution_type->recurrence_unit == 'month') {
                $end_date->addMonths($item);
            }
            if ($contribution_type->recurrence_unit == 'year') {
                $end_date->addYears($item);
            }

            $description = $this->contributionDescription($end_date, $contribution_type);

            foreach ($contributions as $i) {
                if ($i->end_at->isSameDay($end_date)) {
                    $payments = Payment::where('contribution_id', $i->id)->get();
                    $paid = $payments->sum('amount');

                    return (object)[
                        "id" => $i->id,
                        "contribution_date" => $i->contribution_date,
                        "end_at" => $i->end_at,
                        "description" => $description,
                        "amount" => $i->amount,
                        "paid" => $paid,
                        "balance" => $i->amount - $paid,
                        "status" => $i->status,
                    ];
                }
            }

            return (object)[
                "id" => null,
                "contribution_date" => null,
                "end_at" => $end_date,
                "description" => $description,
                "amount" => $contribution_type->amount ?? null,
                "paid" => 0,
                "balance" => $contribution_type->amount - 0,
                "status" => null,
            ];
        });

        return $all;
    }
}
