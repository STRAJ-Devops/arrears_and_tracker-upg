<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function customer(Request $request)
    {
        $customer_id = $request->customer_id;

        // Get customer details matching the provided customer ID
        $customer_details = DB::table('customers')
            ->join('arrears', 'customers.customer_id', '=', 'arrears.customer_id')
            ->selectRaw('
                customers.names,
                customers.phone,
                arrears.draw_down_balance,
                arrears.savings_balance,
                arrears.group_id,
                (arrears.outsanding_principal + arrears.real_outstanding_interest) as loan_balance,
                (arrears.principal_arrears + arrears.outstanding_interest) as amount_due')
            ->where('customers.customer_id', $customer_id)
            ->orWhere('customers.names', 'like', '%' . $customer_id . '%') // Search by name or phone number
            ->orWhere('customers.phone', 'like', '%' . $customer_id . '%')
            ->get();

        // Check if any customer details are found
        if ($customer_details->isEmpty()) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json($customer_details, 200);
    }


    public function group(Request $request)
    {
        $customer_id = $request->customer_id;

        $group_id = DB::table('arrears')->where('customer_id', $customer_id)->value('group_id');
        // Get customer details matching the provided customer ID
        $customer_details = DB::table('customers')
            ->join('arrears', 'customers.customer_id', '=', 'arrears.customer_id')
            ->selectRaw('
                customers.names,
                customers.phone,
                arrears.draw_down_balance,
                arrears.savings_balance,
                arrears.group_id,
                arrears.group_name,
                arrears.customer_id')
                ->where('lending_type', 'Group')
            ->where('arrears.group_id', $group_id)->get();

        // Check if any customer details are found
        if ($customer_details->isEmpty()) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json($customer_details, 200);
    }
}
