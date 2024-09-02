<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use Inertia\Inertia;
use App\Models\Member;
use Illuminate\Support\Carbon;
use App\Http\Requests\StoreSmsRequest;
use App\Http\Requests\UpdateSmsRequest;

class SmsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->input('search');
        $recipient_type = request()->input('recipient_type');

        $messages = Sms::when($search, function ($query) use ($search) {
            return $query->where('message', 'like', "%{$search}%");
        })->paginate()
            ->withQueryString()
            ->through(fn(Sms $sms) => [
                'id' => $sms->id,
                'message' => $sms->message,
                'sent_at' => Carbon::parse($sms->sent_at),
                'recepients' => $sms->recepients->map(fn(Member $member) => [
                    'id' => $member->id,
                    'name' => $member->name,
                    'phone' => $member->phone,
                ])
            ]);

        $recipients = Member::when($recipient_type, function ($query) use ($recipient_type) {
            return $query->where('type', $recipient_type);
        })
            ->get()
            ->map(fn(Member $member) => [
                'id' => $member->id,
                'name' => sprintf('%s %s', $member->first_name, $member->last_name),
                'phone' => $member->phone,
            ]);

        return Inertia::render('Messaging/Sms/Index', ["messages" => $messages, "recipients" => $recipients]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSmsRequest $request)
    {
        $sms = new Sms();
        $sms->message = $request->message;
        $sms->save();
        $sms->recepients()->sync($request->recepients);
        return redirect()->back()->with('success', 'SMS Received for processing');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSmsRequest $request, Sms $sms)
    {
        $sms->message = $request->message;
        $sms->save();
        return redirect()->back()->with('success', 'SMS Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sms $sms)
    {
        $sms->delete();
        return redirect()->back()->with('success', 'SMS deleted');
    }
}
