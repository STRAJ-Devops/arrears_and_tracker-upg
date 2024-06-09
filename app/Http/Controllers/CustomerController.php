<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function customer(Request $request)
    {
        $customer_id = 311220;

        //get customer name, phone, draw down balance, savings balance and loan balance
        $customer_details = DB::table('customers')
            ->join('arrears', 'customers.customer_id', '=', 'arrears.customer_id')
            ->selectRaw('customers.names,
                arrears.draw_down_balance,
                arrears.savings_balance,
                (arrears.outsanding_principal + arrears.real_outstanding_interest) as loan_balance,
                (arrears.principal_arrears + arrears.outstanding_interest) as amount_due')
            ->where('customers.customer_id', $customer_id)->first();

            if(!$customer_details) {
                return response()->json(['message' => 'Customer not found'], 404);
            }

        return response()->json($customer_details, 200);
    }

}
