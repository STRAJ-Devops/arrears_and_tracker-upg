<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Arrear;

class CalendarController extends Controller
{
    public function getcalender(Request $request)
    {
        $events = [];

        $arrears = Arrear::join('customers', 'arrears.customer_id', '=', 'customers.customer_id')
            ->select('arrears.next_repayment_date', 'arrears.customer_id', 'customers.names as customer_name')
            ->get();

        foreach ($arrears as $arrear) {
            //if $arrear->next_repayment_date is "", set it to today
            if ($arrear->next_repayment_date == "") {
                $arrear->next_repayment_date = date('Y-m-d');
            }
            //convert date  and in "2024-11-12"
            $next_repayment_date = date('Y-m-d', strtotime($arrear->next_repayment_date));
            //then convert it back to string

            array_push($events, [
                'title' => $arrear->customer_id . ' - ' . $arrear->customer_name,
                'next_repayment_date' => $next_repayment_date,
            ]);
        }

        return response()->json($events);
    }
}
