<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use Inertia\Inertia;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
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
        })->orderBy('id', 'DESC')
            ->paginate()
            ->withQueryString()
            ->through(function (Sms $sms) {

                $contacts = [];

                if ($sms->recipients) {
                    $contacts = $sms->recipients->map(function (Member $member) {
                        return [
                            'id' => $member->id,
                            'name' => sprintf('%s %s', $member->first_name, $member->last_name),
                            'phone' => $member->phone,
                            'status' => $member->pivot->status,
                        ];
                    });
                }

                return [
                    'id' => $sms->id,
                    'message' => $sms->message,
                    'sent_at' => Carbon::parse($sms->sent_at)->isoFormat('ddd Do MMM, Y h:mm:ss A'),
                    'recipients' => $contacts
                ];
            });

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
        $sms->recipients()->sync($request->recipients);
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

    function callback(Request $request): void
    {
        $data = $request->all();

        Log::info(json_encode($data));

        // foreach ($data as $recipient) {
        //     MemberSms::where('messageId', $recipient['messageId'])
        //         ->update(['status' => $recipient['status']]);
        // }

        // return response()->json(['status' => 'success']);
    }
}
