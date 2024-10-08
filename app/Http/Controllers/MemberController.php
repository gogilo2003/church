<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Member;
use Illuminate\Support\Carbon;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UploadPhotoRequest;
use App\Http\Requests\UpdateMemberRequest;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->input('search');

        $members = Member::with('groups')->when($search, function () use ($search) {})->paginate(8);
        return Inertia::render('Members/Index', ['members' => $members, 'search' => $search]);
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
    public function store(StoreMemberRequest $request)
    {
        $member = new Member();
        $member->first_name = $request->first_name;
        $member->last_name = $request->last_name;
        $member->email = $request->email;
        $member->phone = $request->phone;
        $member->box_no = $request->box_no;
        $member->post_code = $request->post_code;
        $member->town = $request->town;
        $member->address = $request->address;
        $member->date_of_birth = Carbon::parse($request->date_of_birth);
        $member->gender = $request->gender;
        $member->save();

        return redirect()->back()->with('success', 'Member saved');
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        $member->first_name = $request->first_name;
        $member->last_name = $request->last_name;
        $member->email = $request->email;
        $member->phone = $request->phone;
        $member->box_no = $request->box_no;
        $member->post_code = $request->post_code;
        $member->town = $request->town;
        $member->address = $request->address;
        $member->date_of_birth = Carbon::parse($request->date_of_birth);
        $member->gender = $request->gender;
        $member->save();

        return redirect()->back()->with('success', 'Member Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        if ($member->delete()) {
            return redirect()->back()->with('success', 'Member deleted');
        }
        return redirect()->back()->with('danger', 'An error occurred! member was not deleted');
    }

    /**
     * Upload member photo
     */
    function photo(UploadPhotoRequest $request)
    {
        $member = Member::find($request->id);
        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }
        $member->photo = $request->photo->storePublicly('members', ['disk' => 'public']);
        $member->save();

        return redirect()->back()->with('success', 'Photo Updated');
    }

    function download()
    {
        // Fetch all members
        $members = Member::all()->map(function (Member $member) {
            // Check if the member has a photo, otherwise use a placeholder based on gender
            if (!empty($member->photo)) {
                if (Storage::disk('public')->exists($member->photo)) {
                    $photoPath = Storage::disk('public')->path($member->photo);
                } else {
                    $photoPath = Storage::disk('public')->path('members/' . ($member->gender ? 'female-placeholder.png' : 'male-placeholder.png'));
                }
            } else {
                $photoPath = Storage::disk('public')->path('members/' . ($member->gender ? 'female-placeholder.png' : 'male-placeholder.png'));
            }

            // Get the file size
            $photoSize = filesize($photoPath);

            // Convert the photo to base64
            $photoBase64 = base64_encode(file_get_contents($photoPath));
            $photoMimeType = mime_content_type($photoPath);

            $date = Carbon::parse($member->date_of_birth);

            return (object)[
                'id' => sprintf("#%s", str_pad($member->id, 4, "0", STR_PAD_LEFT)),
                'name' => "$member->first_name $member->last_name",
                'email' => $member->email,
                'phone' => $member->phone,
                'date_of_birth' => sprintf("%s(%s)", $date->isoFormat('ddd, Do MMM, Y'), $date->age),
                'gender' => $member->gender ? 'Female' : 'Male',
                'photo' => "data:$photoMimeType;base64,$photoBase64",
                'photo_size' => $this->formatSizeUnits($photoSize),  // Include human-readable photo size
            ];
        });

        // Load the Blade template and pass the data
        $pdf = SnappyPdf::loadView('reports.member_report', compact('members'));

        // Download the PDF
        return $pdf->download('member_report.pdf');
    }

    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
