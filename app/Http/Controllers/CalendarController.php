<?php

namespace App\Http\Controllers;

use App\Models\Arrear;
// Add Carbon
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar');
    }

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
                'start' => $next_repayment_date,
                'end' => $next_repayment_date,
                'className' => 'bg-warning',
            ]);
        }
        header('Content-Type: application/json');
        echo json_encode($events);
    }

}
