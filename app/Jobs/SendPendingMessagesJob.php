<?php

namespace App\Jobs;

use App\Models\Sms;
use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPendingMessagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $maxRetries = env('SMS_MAX_RETRIES', 3);

        // Get messages that have at least one pending recipient (status != 'Success' and retries < maxRetries)
        $pendingMessages = Sms::whereHas('recipients', function ($query) use ($maxRetries) {
            $query->where('member_sms.status', 'pending')
                ->where('member_sms.retries', '<', $maxRetries);
        })
            ->with('recipients')
            ->get();

        foreach ($pendingMessages as $sms) {
            // Prepare the message
            $message = $sms->message;

            // Filter members who haven't exceeded retries and need to be sent
            $recipients = $sms->recipients->filter(function ($member) use ($sms, $maxRetries) {
                $pivot = $member->pivot;
                return $pivot->status !== 'Success' && $pivot->retries < $maxRetries;
            })->pluck('phone')->implode(',');

            if (empty($recipients)) {
                continue; // Skip if no recipients need to be sent
            }

            // Send the message via Africa's Talking API
            $username = env('SMS_USERNAME');
            $apiKey   = env('SMS_API_KEY');
            $AT       = new AfricasTalking($username, $apiKey);
            $smsService = $AT->sms();
            $from = env('SMS_SENDER_ID', null);

            $result = $smsService->send([
                'to'      => $recipients,
                'message' => $message,
                'from'    => $from,
            ]);

            // Update the sent_at field if it hasn't been set
            if (is_null($sms->sent_at)) {
                $sms->sent_at = Carbon::now();
                $sms->save();
            }

            $recipientsResponse = $result["data"]->SMSMessageData->Recipients;
            foreach ($recipientsResponse as $recipient) {
                $phone = str_replace("+254", "", $recipient->number);
                $member = $sms->recipients()->where('phone', 'like', "%$phone%")->first();
                if ($recipient->status === 'Success') {
                    // Update status and reset retries on success
                    $sms->recipients()->updateExistingPivot($member->id, [
                        'status'    => $recipient->status,
                        'messageId' => $recipient->messageId,
                        'retries'   => 0,
                    ]);
                } else {
                    // Increment retries and flag as failed if exceeded max retries
                    $retries = $member->pivot->retries + 1;
                    $sms->recipients()->updateExistingPivot($member->id, [
                        'status'    => $retries >= $maxRetries ? 'Failed' : $recipient->status,
                        'messageId' => $recipient->messageId,
                        'retries'   => $retries,
                    ]);
                }
            }
        }
    }
}
