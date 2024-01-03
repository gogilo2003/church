<?php

namespace App\Listeners;

use App\Models\Contribution;
use App\Events\PaymentRegistered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateContributionStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentRegistered $event): void
    {
        $payment = $event->payment;

        $contribution = Contribution::find($payment->contribution_id);

        $status = $contribution->amount
            ? ($contribution->amount == $contribution->payments->sum('amount') ? 'complete'
                : (
                    $contribution->amount > $contribution->payments->sum('amount')
                    ? 'partial'
                    : 'pending'
                )
            )
            : 'pending';

        $contribution->update('contribution_status', $status);
    }
}
