<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function customer(Request $request)
    {
        $customer_id = $request->input('customer_id');

        //get customer name, phone, draw down balance, savings balance and loan balance
        $customer_details = DB::table('customers')
            ->join('arrears', 'customers.customer_id', '=', 'arrears.customer_id')
            ->selectRaw('customers.names,
            arrears.draw_down_balance,
            arrears.savings_balance,
            (arrears.outstanding_principal + arrears.outstanding_interest) as loan_balance
            (arrears.principal_in_arrears+outstanding_interest as amount_due');
    }
}
