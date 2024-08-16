<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Arrear;
use Illuminate\Support\Facades\DB;
class CalendarController extends Controller
{
    public function getcalender(Request $request)
    {
        $events = [];

        // Get arrear groups from the database
        $arrear_groups = Arrear::where('lending_type', 'Group')
            ->groupBy('group_id', 'next_repayment_date')
            ->select('group_id', 'next_repayment_date', DB::raw('count(*) as group_count'))
            ->get();

        // Create an array to aggregate group counts by next_repayment_date
        $event_data = [];

        foreach ($arrear_groups as $arrear) {
            // If next_repayment_date is empty, set it to today
            if ($arrear->next_repayment_date == "") {
                $arrear->next_repayment_date = date('Y-m-d');
            }
            // Convert the next_repayment_date to "Y-m-d"
            $next_repayment_date = date('Y-m-d', strtotime($arrear->next_repayment_date));

            // Aggregate group counts by next_repayment_date
            if (!isset($event_data[$next_repayment_date])) {
                $event_data[$next_repayment_date] = 0;
            }
            $event_data[$next_repayment_date] += $arrear->group_count;
        }

        // Convert aggregated data into events
        foreach ($event_data as $date => $group_count) {
            array_push($events, [
                'title' => $group_count . ' Group(s) to repay',
                'start' => $date,
                'end' => $date,
                'className' => 'bg-warning',
            ]);
        }

        return response()->json(["status" => true, "data" => $events]);
    }

}
