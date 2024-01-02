<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Member;
use App\Models\Attendance;
use App\Http\Requests\MarkAttendanceRequest;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendances = Attendance::with('members')->get();
        return Inertia::render('Attendances/Index', ['attendances' => $attendances]);
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
    public function store(StoreAttendanceRequest $request)
    {
        $attendance = new Attendance();
        $attendance->title = $request->title;
        $attendance->attendance_date = $request->attendance_date;
        $request->user()->attendances()->save($attendance);

        return redirect()->route('attendance-mark', $attendance->id)->with('success', 'Attendance started');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        $attendance->title = $request->title;
        $attendance->attendance_date = $request->attendance_date;
        $request->user()->attendances()->save($attendance);

        return redirect()->route('attendance-mark', $attendance->id)->with('success', 'Attendance updated');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function mark(Attendance $attendance)
    {
        $attendance->load('members');
        $members = Member::all()->map(fn ($member) => [
            "id" => $member->id,
            "name" => $member->first_name . ' ' . $member->last_name,
            "phone" => $member->phone,
            "email" => $member->email,
            "age" => Carbon::parse($member->date_of_birth)->age,
            "photo" => $member->photo_url,
        ]);
        return Inertia::render('Attendances/Mark', ['attendance' => [
            "id" => $attendance->id,
            "title" => $attendance->title,
            "attendance_date" => Carbon::parse($attendance->attendance_date)->format('D d-M-Y'),
            "members" => $attendance->members->pluck('id'),
        ], 'members' => $members]);
    }

    /**
     * Mark the specified resource in storage.
     */
    public function markPost(MarkAttendanceRequest $request, Attendance $attendance)
    {
        $attendance->members()->sync($request->members);
        return redirect()->back()->with('success', 'Attendance Marked');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
