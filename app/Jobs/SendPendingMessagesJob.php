<?php

namespace App\Jobs;

use App\Models\Sms;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
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
        // Get messages that are pending (sent_at is null) and either scheduled to be sent now or immediately
        $pendingMessages = Sms::whereNull('sent_at')
            ->where(function ($query) {
                $query->whereNull('send_at') // Immediate sending
                    ->orWhere('send_at', '<=', Carbon::now()); // Scheduled sending
            })
            ->with('members')
            ->get();

        foreach ($pendingMessages as $sms) {
            // Prepare the message and recipients
            $message = $sms->message;
            $recipients = $sms->members->pluck('phone')->implode(',');

            // Send the message via Africa's Talking API
            $username = env('SMS_USERNAME');
            $apiKey   = env('SMS_API_KEY');
            $AT       = new AfricasTalking($username, $apiKey);
            $smsService = $AT->sms();
            $from = "myShortCode or mySenderId";

            $result = $smsService->send([
                'to'      => $recipients,
                'message' => $message,
                'from'    => $from,
            ]);

            // Update the sent_at field to mark as sent
            $sms->sent_at = Carbon::now();
            $sms->save();

            // Process the response to update pivot table (member_sms)
            $recipientsResponse = $result['SMSMessageData']['Recipients'];
            foreach ($recipientsResponse as $recipient) {
                $sms->members()->where('phone', $recipient['number'])
                    ->updateExistingPivot($sms->id, [
                        'status'    => $recipient['status'],
                        'messageId' => $recipient['messageId']
                    ]);
            }
        }
    }
}
