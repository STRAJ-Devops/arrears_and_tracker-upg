<?php

namespace App\Http\Controllers;

use App\Models\Arrear;
use Illuminate\Http\Request;

class ExpectedController extends Controller
{
    public function index()
    {

        return view('expected-repayments');
    }

    public function getAllExpectedRepayments()
    {
        $arrears = Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->get();

        // Loop through each arrear to calculate expected principal and interest
        foreach ($arrears as $arrear) {
            // Assuming $this->next_repayment_principal and $this->next_repayment_interest are properties of the Arrear model
            $expectedPrincipal = $arrear->principal_in_arrears + $arrear->next_repayment_principal;
            $expectedInterest = $arrear->interest_in_arrears + $arrear->next_repayment_interest;
            $expected_total = $expectedPrincipal + $expectedInterest;

            // Assign the calculated values back to the arrear object
            $arrear->expected_principal = $expectedPrincipal;
            $arrear->expected_interest = $expectedInterest;
            $arrear->expected_total = $expected_total;
        }

        return response()->json(['arrears' => $arrears], 200);
    }


    public function group_by(Request $request)
    {
        // Check if request has group as parameter
        if ($request->has('group')) {
            if ($request->group == 'staff_id') {
                $arrears = auth()->user()->user_type == 1 ? Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->get()->groupBy('staff_id') : Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->where('staff_id', auth()->user()->staff_id)->get()->groupBy('staff_id');
                $groupKey = 'staff_id';
                $nameField = 'officer';
                $nameAttribute = 'names';
            } else if ($request->group == 'branch_id') {
                $arrears = auth()->user()->user_type == 1 ? Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->get()->groupBy('branch_id') : Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->where('staff_id', auth()->user()->staff_id)->get()->groupBy('branch_id');
                $groupKey = 'branch_id';
                $nameField = 'branch';
                $nameAttribute = 'branch_name';
            } else if ($request->group == 'region_id') {
                $arrears = auth()->user()->user_type == 1 ? Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->get()->groupBy('region_id') : Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->where('staff_id', auth()->user()->staff_id)->get()->groupBy('region_id');
                $groupKey = 'region_id';
                $nameField = 'region';
                $nameAttribute = 'region_name';
            } else if ($request->group == 'loan_product') {
                $arrears = auth()->user()->user_type == 1 ? Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->get()->groupBy('product_id') : Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->where('staff_id', auth()->user()->staff_id)->get()->groupBy('product_id');
                $groupKey = 'product_id';
                $nameField = 'product';
                $nameAttribute = 'product_name';
            } else if ($request->group == 'gender') {
                $arrears = auth()->user()->user_type == 1 ? Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->get()->groupBy('gender') : Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->where('staff_id', auth()->user()->staff_id)->get()->groupBy('gender');
                $groupKey = 'gender';
                $nameField = 'gender';
                $nameAttribute = "None";
            } else if ($request->group == 'district') {
                $arrears = auth()->user()->user_type == 1 ? Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->get()->groupBy('district_id') : Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->where('staff_id', auth()->user()->staff_id)->get()->groupBy('district_id');
                $groupKey = 'district_id';
                $nameField = 'district';
                $nameAttribute = 'district_name';
            } else if ($request->group == 'sub_county') {
                $arrears = auth()->user()->user_type == 1 ? Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->get()->groupBy('subcounty_id') : Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->where('staff_id', auth()->user()->staff_id)->get()->groupBy('subcounty_id');
                $groupKey = 'subcounty_id';
                $nameField = 'sub_county';
                $nameAttribute = 'subcounty_name';
            } else if ($request->group == 'village') {
                $arrears = auth()->user()->user_type == 1?Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->get()->groupBy('village_id'): Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->where('staff_id', auth()->user()->staff_id)->get()->groupBy('village_id');
                $groupKey = 'village_id';
                $nameField = 'village';
                $nameAttribute = 'village_name';
            } else if ($request->group == 'age') {
                $arrears = auth()->user()->user_type == 1 ? Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->get()->groupBy(function ($arrear) {
                    $age = $arrear->number_of_days_late;
                    if ($age >= 1 && $age <= 30) {
                        return '1-30';
                    } elseif ($age >= 31 && $age <= 60) {
                        return '31-60';
                    } elseif ($age >= 61 && $age <= 90) {
                        return '61-90';
                    } elseif ($age >= 91 && $age <= 120) {
                        return '91-120';
                    } elseif ($age >= 121 && $age <= 150) {
                        return '121-150';
                    }    elseif ($age >= 151 && $age <= 180) {
                        return '151-180';
                    }  else {
                        return '180+';
                    }
                }) : Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->where("staff_id", auth()->user()->staff_id)->get()->groupBy(function ($arrear) {
                    $age = $arrear->number_of_days_late;
                    if ($age >= 1 && $age <= 30) {
                        return '1-30';
                    } elseif ($age >= 31 && $age <= 60) {
                        return '31-60';
                    } elseif ($age >= 61 && $age <= 90) {
                        return '61-90';
                    } elseif ($age >= 91 && $age <= 120) {
                        return '91-120';
                    } elseif ($age >= 121 && $age <= 150) {
                        return '121-150';
                    }    elseif ($age >= 151 && $age <= 180) {
                        return '151-180';
                    }  else {
                        return '180+';
                    }
                });
                $groupKey = 'age';
                $nameField = null;
                $nameAttribute = null;
            } else if ($request->group == 'client') {
                $arrears = auth()->user()->user_type == 1 ? Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->get()->groupBy('customer_id') : Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->where("staff_id", auth()->user()->staff_id)->get()->groupBy('customer_id');
                $groupKey = 'client_id';
                $nameField = 'customer';
                $nameAttribute = 'names';
            } else {
                // Default to group by staff_id
                $arrears = auth()->user()->user_type == 1 ? Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->get()->groupBy('staff_id') : Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->where("staff_id", auth()->user()->staff_id)->get()->groupBy('staff_id');
                $groupKey = 'staff_id';
                $nameField = 'officer';
                $nameAttribute = 'names';
            }
        } else {
            // Default to group by staff_id if 'group' parameter is not provided
            $arrears = auth()->user()->user_type == 1 ? Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->get()->groupBy('staff_id') : Arrear::whereRaw('principal_arrears + interest_in_arrears = 0')->where("staff_id", auth()->user()->staff_id)->get()->groupBy('staff_id');
            $groupKey = 'staff_id';
            $nameField = 'officer';
        }

        // Initialize data array
        $data = [];

        // Iterate through grouped arrears and calculate totals
        foreach ($arrears as $key => $arrear) {
            $total_principle_arrears = $arrear->sum('principal_arrears');
            $total_interest_arrears = $arrear->sum('outstanding_interest');
            $total_next_repayment_principal = $arrear->sum('next_repayment_principal');
            $total_next_repayment_interest = $arrear->sum('next_repayment_interest');
            $expectedPrincipal = $total_principle_arrears + $total_next_repayment_principal;
            $expectedInterest = $total_interest_arrears + $total_next_repayment_interest;
            $expected_total = $expectedPrincipal + $expectedInterest;
            $clients_in_arrears = $arrear->where('number_of_days_late', '>', 0)->count();
            $total_clients = $arrear->sum('number_of_group_members');
            $names = $arrear->first()->$nameField->$nameAttribute ?? "None"; // Fetch name based on grouping key
            $next_repayment_date = $arrear->first()->next_repayment_date;
            $phone_number = $arrear->first()->$nameField->phone ?? "None"; // Fetch name based on grouping key
            $number_of_comments = $arrear->first()->customer->comments->count();
            $amount_disbursed = $arrear->sum('amount_disbursed');
            $data[] = [
                'arrear_id' => $arrear->first()->id, // Fetch arrear id for the first record in the group
                'customer_id' => $arrear->first()->customer->customer_id, // Fetch customer id for the first record in the group
                'group_key' => $key,
                'expected_principal' => $expectedPrincipal,
                'expected_interest' => $expectedInterest,
                'expected_total' => $expected_total??0,
                'clients_in_arrears' => $clients_in_arrears,
                'total_clients' => $total_clients,
                'names' => $names,
                'next_repayment_date' => $next_repayment_date,
                'phone_number' => $phone_number,
                'number_of_comments' => $number_of_comments,
                'amount_disbursed' => $amount_disbursed
            ];
        }

        // Return JSON response with data and success message
        return response()->json(['data' => $data, 'message' => 'success'], 200);
    }

}
