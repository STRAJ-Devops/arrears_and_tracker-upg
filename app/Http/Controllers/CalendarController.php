<?php

namespace App\Http\Controllers;

use App\Models\Arrear;
// Add Carbon
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar');
    }

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

            array_push($events, [
                'title' => $arrear->group_count . ' Group(s) to repay',
                'start' => $next_repayment_date,
                'end' => $next_repayment_date,
                'className' => 'bg-warning',
            ]);
        }
        header('Content-Type: application/json');
        echo json_encode($events);
    }

}
