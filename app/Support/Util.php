<?php

namespace App\Support;

use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Util
{
    static function getTitheSummary()
    {
        $now = Carbon::now();
        $startDate = $now->subWeeks(9)->startOfWeek(); // Get the start date 10 weeks ago

        $tithes = DB::table('tithes')
            ->select(
                DB::raw('YEARWEEK(tithed_on, 1) as week'), // ISO-8601 week number
                DB::raw('SUM(amount) as total')
            )
            ->where('tithed_on', '>=', $startDate)
            ->groupBy('week')
            ->orderBy('week', 'asc')
            ->get();

        $weeks = [];
        $currentWeek = Carbon::now()->startOfWeek();

        for ($i = 9; $i >= 0; $i--) {
            $weekNumber = $currentWeek->copy()->subWeeks($i)->format('oW'); // ISO-8601 year and week number
            $weeks[$weekNumber] = 0;
        }

        foreach ($tithes as $tithe) {
            $weeks[$tithe->week] = $tithe->total;
        }

        $result = [];
        foreach ($weeks as $week => $total) {
            $weekStartDate = Carbon::now()->startOfWeek()->subWeeks(Carbon::now()->format('oW') - $week)->format('Y-m-d');
            $result[] = [
                'week' => $weekStartDate,
                'total' => $total
            ];
        }

        return $result;
    }

    /**
     * Summary of getOfferingSummary
     * @return array<int|mixed|string>[]
     */
    static function getOfferingSummary()
    {
        $now = Carbon::now();
        $startDate = $now->subWeeks(9)->startOfWeek(); // Get the start date 10 weeks ago

        $offerings = DB::table('offerings')
            ->select(
                DB::raw('YEARWEEK(offering_date, 1) as week'), // ISO-8601 week number
                DB::raw('SUM(amount) as total')
            )
            ->where('offering_date', '>=', $startDate)
            ->groupBy('week')
            ->orderBy('week', 'asc')
            ->get();

        $weeks = [];
        $currentWeek = Carbon::now()->startOfWeek();

        for ($i = 9; $i >= 0; $i--) {
            $weekNumber = $currentWeek->copy()->subWeeks($i)->format('oW'); // ISO-8601 year and week number
            $weeks[$weekNumber] = 0;
        }

        foreach ($offerings as $offering) {
            $weeks[$offering->week] = $offering->total;
        }

        $result = [];
        foreach ($weeks as $week => $total) {
            $weekStartDate = Carbon::now()->startOfWeek()->subWeeks(Carbon::now()->format('oW') - $week)->format('Y-m-d');
            $result[] = [
                'week' => $weekStartDate,
                'total' => $total
            ];
        }

        return $result;
    }

    static function getPaymentSummary()
    {
        $now = Carbon::now();
        $startDate = $now->subWeeks(9)->startOfWeek(); // Get the start date 10 weeks ago

        $payments = DB::table('payments')
            ->select(
                DB::raw('YEARWEEK(created_at, 1) as week'), // ISO-8601 week number
                DB::raw('SUM(amount) as total')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('week')
            ->orderBy('week', 'asc')
            ->get();

        $weeks = [];
        $currentWeek = Carbon::now()->startOfWeek();

        for ($i = 9; $i >= 0; $i--) {
            $weekNumber = $currentWeek->copy()->subWeeks($i)->format('oW'); // ISO-8601 year and week number
            $weeks[$weekNumber] = 0;
        }

        foreach ($payments as $payment) {
            $weeks[$payment->week] = $payment->total;
        }


        $result = [];
        foreach ($weeks as $week => $total) {
            $weekStartDate = Carbon::now()->startOfWeek()->subWeeks(Carbon::now()->format('oW') - $week)->format('Y-m-d');
            $result[] = [
                'week' => $weekStartDate,
                'total' => $total
            ];
        }

        return $result;
    }

    static function getAttendanceSummary()
    {
        $attendances = Attendance::with('members')
            ->orderBy('attendance_date', 'desc')
            ->take(10)
            ->get();

        // Optionally, process the data if needed
        $attendanceData = $attendances->map(function ($attendance) {
            return [
                'label' => $attendance->attendance_date,
                'total' => $attendance->members->count()
            ];
        });

        return $attendanceData;
    }
}
