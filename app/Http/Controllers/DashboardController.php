<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use NumberFormatter;
use App\Models\Tithe;
use App\Models\Member;
use App\Models\Offering;
use App\Models\Contribution;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Support\Util;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Create a NumberFormatter instance for currency formatting
        $formatter = new NumberFormatter('en_KE', NumberFormatter::CURRENCY);

        // Format the amount as currency
        $tithe = $formatter->formatCurrency(Tithe::all()->sum('amount'), 'KES');
        $offering = $formatter->formatCurrency(Offering::all()->sum('amount'), 'KES');
        $contribution = $formatter->formatCurrency(Payment::all()->sum('amount'), 'KES');

        $stats = [
            [
                "name" => "tithes",
                "value" => $tithe
            ],
            [
                "name" => "offerings",
                "value" => $offering
            ],
            [
                "name" => "contributions",
                "value" => $contribution
            ],
            [
                "name" => "members",
                "value" => Member::all()->count()
            ],
        ];

        $data = [
            'stats' => $stats,
            'tithes' => Util::getTitheSummary(),
            'offerings' => Util::getOfferingSummary(),
            'contributions' => Util::getPaymentSummary(),
            'attendances' => Util::getAttendanceSummary(),
        ];
        return Inertia::render('Dashboard', $data);
    }
}
