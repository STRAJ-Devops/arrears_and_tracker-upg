<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Arrear;
use App\Models\SCVCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CustomerController extends Controller
{
    public function customer(Request $request)
    {
        $customer_id = $request->customer_id;
        $search_by = $request->search_by;
        $today = date('d-M-y');
        //check if search_by is customer_id, phone or name

        if ($search_by == 'customer_id') {
            $customer_details = DB::table('customers')
                ->join('arrears', 'customers.customer_id', '=', 'arrears.customer_id')
                ->join('products', 'arrears.product_id', '=', 'products.product_id')
                ->selectRaw('
                customers.names,
                customers.phone,
                products.product_name,
                arrears.draw_down_balance,
                arrears.savings_balance,
                IF(arrears.group_name = "", NULL, arrears.group_id) as group_id,
                (arrears.outsanding_principal + arrears.real_outstanding_interest) as loan_balance,
                (arrears.principal_arrears + arrears.outstanding_interest +
                IF(arrears.next_repayment_date = ? OR arrears.next_repayment_date = "", arrears.next_repayment_principal + arrears.next_repayment_interest, 0)) as amount_due', [$today])
                ->where('customers.customer_id', $customer_id)
                ->get();
            //new update

        } elseif ($search_by == 'phone') {
            $customer_details = DB::table('customers')
                ->join('arrears', 'customers.customer_id', '=', 'arrears.customer_id')
                ->join('products', 'arrears.product_id', '=', 'products.product_id')
                ->selectRaw('
                    customers.names,
                    customers.phone,
                    products.product_name,
                    arrears.draw_down_balance,
                    arrears.savings_balance,
                    IF(arrears.group_name = "", NULL, arrears.group_id) as group_id,
                    (arrears.outsanding_principal + arrears.real_outstanding_interest) as loan_balance,
                    (arrears.principal_arrears + arrears.outstanding_interest +
                IF(arrears.next_repayment_date = ? OR arrears.next_repayment_date = "", arrears.next_repayment_principal + arrears.next_repayment_interest, 0)) as amount_due', [$today])
                ->where('customers.phone', 'like', '%' . $customer_id . '%')
                ->get();
        } elseif ($search_by == 'name') {
            $customer_details = DB::table('customers')
                ->join('arrears', 'customers.customer_id', '=', 'arrears.customer_id')
                ->join('products', 'arrears.product_id', '=', 'products.product_id')
                ->selectRaw('
                    customers.names,
                    customers.phone,
                    products.product_name,
                    arrears.draw_down_balance,
                    arrears.savings_balance,
                    IF(arrears.group_name = "", NULL, arrears.group_id) as group_id,
                    (arrears.outsanding_principal + arrears.real_outstanding_interest) as loan_balance,
                    (arrears.principal_arrears + arrears.outstanding_interest +
                IF(arrears.next_repayment_date = ? OR arrears.next_repayment_date = "", arrears.next_repayment_principal + arrears.next_repayment_interest, 0)) as amount_due', [$today])
                ->where('customers.names', 'like', '%' . $customer_id . '%')
                ->get();
        } else if ($search_by == 'group_id') {

            $customer_details = DB::table('customers')
                ->join('arrears', 'customers.customer_id', '=', 'arrears.customer_id')
                ->join('products', 'arrears.product_id', '=', 'products.product_id')
                ->selectRaw('
                    customers.names,
                    customers.phone,
                    products.product_name,
                    arrears.draw_down_balance,
                    arrears.savings_balance,
                    IF(arrears.group_name = "", NULL, arrears.group_id) as group_id,
                    (arrears.outsanding_principal + arrears.real_outstanding_interest) as loan_balance,
                    (arrears.principal_arrears + arrears.outstanding_interest +
                IF(arrears.next_repayment_date = ? OR arrears.next_repayment_date = "", arrears.next_repayment_principal + arrears.next_repayment_interest, 0)) as amount_due', [$today])
                ->where('arrears.group_id', $customer_id)
                ->get();
        } else {
            return response()->json(['message' => 'Invalid search_by parameter'], 400);
        }

        return response()->json($customer_details, 200);
    }

    public function group(Request $request)
    {
        $customer_id = $request->customer_id;
        $search_by = $request->search_by;

        //find the group_id of the customer basing on the search_by parameter
        if ($search_by == 'customer_id') {
            $record = DB::table('customers')
                ->join('arrears', 'customers.customer_id', '=', 'arrears.customer_id')
                ->selectRaw('arrears.group_id, customers.phone')
                ->where('customers.customer_id', $customer_id)
                ->first();
        } elseif ($search_by == 'phone') {
            $record = DB::table('customers')
                ->join('arrears', 'customers.customer_id', '=', 'arrears.customer_id')
                ->selectRaw('arrears.group_id, customers.phone')
                ->where('customers.phone', 'like', '%' . $customer_id . '%')
                ->first();
        } else if ($search_by == 'group_name') {
            $record = DB::table('arrears')
                ->selectRaw('group_id')
                ->where('group_name', 'like', '%' . $customer_id . '%')
                ->first();
        } else if ($search_by == 'group_id') {
            $record = DB::table('arrears')
                ->selectRaw('group_id')
                ->where('group_id', $customer_id)
                ->first();
        } else {
            return response()->json(['message' => 'Invalid search_by parameter'], 400);
        }

        if (!$record) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        // Get the group id
        $group_id = $record->group_id;

        // Get customer details matching the provided customer ID and group_id
        $customer_details = DB::table('customers')
            ->join('arrears', 'customers.customer_id', '=', 'arrears.customer_id')
            ->selectRaw('
                customers.names,
                arrears.group_id,
                arrears.group_name,
                arrears.customer_id,
                customers.phone
            ')
            ->where('arrears.lending_type', 'Group')
            ->where(function ($query) use ($customer_id, $group_id) {
                $query->where('arrears.group_id', $group_id);
            })
            ->get();

        // Check if any customer details are found
        if ($customer_details->isEmpty()) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json($customer_details, 200);
    }

    public function online_customer(Request $request)
    {
        // attempt to fetch data online
        $search_by = $request->search_by;
        $search_payload = $request->customer_id;
        if ($search_by == 'customer_id') {
            $search_criteria = 'customerNo';
        } elseif ($search_by == 'officer_no') {
            $search_criteria = 'officerNo';
        } elseif ($search_by == 'contract_no') {
            $search_criteria = 'contractNo';
        } elseif ($search_by == 'account_no') {
            $search_criteria = 'accountNo';
        } elseif ($search_by == 'phone_no') {
            $search_criteria = 'phoneNo';
        } elseif ($search_by == 'name') {
            $search_criteria = 'name';
        } else {
            $search_criteria = 'customerNo';
        }

        try {
            $online_request = Http::timeout(90)->get('https://test.ug.vft24.org/crmapi/v1/loan/scv/' . $search_criteria . '/' . $search_payload);
        } catch (\Throwable $th) {
            $online_request = null;
        }


        if ($online_request && $online_request->successful() && $online_request->json('data')) {
            $data = $online_request->json('data');
            SCVCache::updateOrCreate(
                ['param' => $search_criteria, 'key' => $search_payload],
                ['data' => $data]
            );
            // foreach ($data as $customer) {
            //     # code...
            //     Arrear::updateOrCreate(
            //         ['customer_id' => $customer['customerId']],
            //         [
            //             'customer_name' => $customer['customerName'],
            //             'product_id' => $customer['productId'],
            //             'draw_down_balance' => $customer['drawDownBalance'],
            //             'savings_balance' => $customer['compSavingsBal'],
            //             'loan_balance' => $customer['loanBal'],
            //             'amount_due_today' => $customer['amountDueToday'],
            //             'phone' => $customer['phoneNo'],
            //             'group_id' => $customer['groupId'],
            //             'outstanding_principal' => $customer['outstandingPrincipal'],
            //             'outstanding_interest' => $customer['outstandingInterest'],
            //             'principal_arrears' => $customer['principalArrears'],
            //             'interest_arrears' => $customer['interestInArrears'],
            //             'lending_type' => $customer['lendingType'],
            //             'gender' => $customer['gender'],
            //             'disbursement_date' => $customer['disbursementDate'],
            //             'staff_id' => $customer['staffId'],
            //             'branch_id' => $customer['branchId'],
            //             'village_id' => $customer['villageId'],
            //             'maturity_date' => $customer['maturityDate']
            //         ]
            //     );
            // }
            return response()->json($data);
        } else {
            $cache = SCVCache::where('param', $search_criteria)
                ->where('key', $search_payload)
                ->latest()
                ->first()?->data;
            if ($cache) {
                return response()->json($cache);
            } else {
                return response()->json("Not found ", 400);
            }
        }
    }
}
