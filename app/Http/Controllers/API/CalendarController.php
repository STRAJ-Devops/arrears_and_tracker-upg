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

        $arrear_groups = Arrear::where('lending_type', 'Group')
            ->groupBy('group_id', 'next_repayment_date')
            ->select('group_id', 'next_repayment_date', DB::raw('count(*) as group_count'))
            ->get();

        foreach ($arrear_groups as $arrear) {
            //if $arrear->next_repayment_date is "", set it to today
            if ($arrear->next_repayment_date == "") {
                $arrear->next_repayment_date = date('Y-m-d');
            }
            //convert date  and in "2024-11-12"
            $next_repayment_date = date('Y-m-d', strtotime($arrear->next_repayment_date));
            //then convert it back to string

            //check if the start same as the repayment date already exists
            $exists = false;
            foreach ($events as $event) {
                if ($event['start'] == $next_repayment_date) {
                    $exists = true;
                    break;
                }
            }

            if ($exists) {
                //just sum the group count
                foreach ($events as $key => $event) {
                    if ($event['start'] == $next_repayment_date) {
                        //get new group count
                        $group_count = $arrear->group_count + $event['group_count'];

                        //update the group count
                        $events[$key]['group_count'] = $group_count;

                        //update the title
                        $events[$key]['title'] = $group_count . ' Group(s) to repay';
                    }
                }
            } else {
            array_push($events, [
                'title' => $arrear->group_count . ' Group(s) to repay',
                'start' => $next_repayment_date,
                'end' => $next_repayment_date,
                'className' => 'bg-warning',
                'group_count' => $arrear->group_count
            ]);
        }
        }
        return response()->json(["status" => true, "data" => $events]);

    }
}
