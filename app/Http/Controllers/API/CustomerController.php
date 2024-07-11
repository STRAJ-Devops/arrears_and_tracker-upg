<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function customer(Request $request)
    {
        $customer_id = $request->customer_id;
        $search_by = $request->search_by;

        //check if search_by is customer_id, phone or name

        if ($search_by == 'customer_id') {
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
                ->get();
        } elseif ($search_by == 'phone') {
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
                ->where('customers.phone', 'like', '%' . $customer_id . '%')
                ->get();
        } elseif ($search_by == 'name') {
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
                ->where('customers.names', 'like', '%' . $customer_id . '%')
                ->get();
        } else if ($search_by == 'group_id') {

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
                ->where('arrears.group_id', $customer_id)
                ->get();

        } else {
            return response()->json(['message' => 'Invalid search_by parameter'], 400);
        }

        return response()->json($customer_details, 200);
    }
}
