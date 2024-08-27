<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arrear;
use Illuminate\Support\Facades\DB;


class MaturityLoanController extends Controller
{
    public function index()
    {
        return view('maturity-loans');
    }
    public function group_by(Request $request)
    {
        $currentMonthYear = DB::table('upload_date')->latest()->value('upload_date') ?? date('M-y');



        //get the next month to
        $nextMonthYear = date('M-y', strtotime($currentMonthYear . ' +1 month'));

        //join arrears and customers table to get the customer names, phone number where maturity date is like %-currentMonthYear, join with products to get product name

        $data = Arrear::join('customers', 'arrears.customer_id', '=', 'customers.customer_id')
        ->join('products', 'arrears.product_id', '=', 'products.product_id')
        ->join('branches', 'arrears.branch_id', '=', 'branches.branch_id')
        ->select('arrears.*', 'customers.names', 'customers.phone', 'products.product_name', 'branches.branch_name')
        ->where('arrears.maturity_date', 'like', "%-$currentMonthYear")
        ->orWhere('arrears.maturity_date', 'like', "%-$nextMonthYear")
        ->orderByRaw("STR_TO_DATE(arrears.maturity_date, '%d-%m-%Y') ASC")
        ->get();

        return response()->json(['data' => $data, 'message' => 'success'], 200);
    }
}
