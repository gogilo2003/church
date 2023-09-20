<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Member;
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
        $members = Member::with('groups')->get();
        return Inertia::render('Members/Index', ['members' => $members]);
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
        $member->date_of_birth = $request->date_of_birth;
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
        $member->date_of_birth = $request->date_of_birth;
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
}
