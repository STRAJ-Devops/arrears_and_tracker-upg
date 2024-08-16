<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Arrear;
use Illuminate\Http\Request;

class ExpectedController extends Controller
{
    public function group_by(Request $request)
    {
        $logged_in_user_type = auth()->user()->user_type;
        $today = date('j-M-y');
        // Check if request has group as parameter
        if ($request->has('group')) {
            if ($request->group == 'staff_id') {
                $arrears = Arrear::where('next_repayment_date', '=', $today)
                //or where next_repayment_date is null
                    ->whereRaw('(principal_arrears + outstanding_interest + next_repayment_principal + next_repayment_interest) !=0 ')->get()->groupBy('staff_id');
                $groupKey = 'staff_id';
                $nameField = 'officer';
                $nameAttribute = 'names';
            } else if ($request->group == 'branch_id') {
                $arrears = Arrear::where('next_repayment_date', '=', $today)->whereRaw('(principal_arrears + outstanding_interest + next_repayment_principal + next_repayment_interest) !=0 ')->get()->groupBy('branch_id');
                $groupKey = 'branch_id';
                $nameField = 'branch';
                $nameAttribute = 'branch_name';
            } else if ($request->group == 'region_id') {
                $arrears = Arrear::where('next_repayment_date', '=', $today)->whereRaw('(principal_arrears + outstanding_interest + next_repayment_principal + next_repayment_interest) !=0 ')->get()->groupBy('region_id');
                $groupKey = 'region_id';
                $nameField = 'region';
                $nameAttribute = 'region_name';
            } else if ($request->group == 'loan_product') {
                $arrears = Arrear::where('next_repayment_date', '=', $today)->whereRaw('(principal_arrears + outstanding_interest + next_repayment_principal + next_repayment_interest) !=0 ')->get()->groupBy('product_id');
                $groupKey = 'product_id';
                $nameField = 'product';
                $nameAttribute = 'product_name';
            } else if ($request->group == 'gender') {
                $arrears = Arrear::where('next_repayment_date', '=', $today)->whereRaw('(principal_arrears + outstanding_interest + next_repayment_principal + next_repayment_interest) !=0 ')->get()->groupBy('gender');
                $groupKey = 'gender';
                $nameField = 'gender';
                $nameAttribute = "None";
            } else if ($request->group == 'district') {
                $arrears = Arrear::where('next_repayment_date', '=', $today)->whereRaw('(principal_arrears + outstanding_interest + next_repayment_principal + next_repayment_interest) !=0 ')->get()->groupBy('district_id');
                $groupKey = 'district_id';
                $nameField = 'district';
                $nameAttribute = 'district_name';
            } else if ($request->group == 'sub_county') {
                $arrears = Arrear::where('next_repayment_date', '=', $today)->whereRaw('(principal_arrears + outstanding_interest + next_repayment_principal + next_repayment_interest) !=0 ')->get()->groupBy('subcounty_id');
                $groupKey = 'subcounty_id';
                $nameField = 'sub_county';
                $nameAttribute = 'subcounty_name';
            } else if ($request->group == 'village') {
                $arrears = Arrear::where('next_repayment_date', '=', $today)->whereRaw('(principal_arrears + outstanding_interest + next_repayment_principal + next_repayment_interest) !=0 ')->get()->groupBy('village_id');
                $groupKey = 'village_id';
                $nameField = 'village';
                $nameAttribute = 'village_name';
            } else if ($request->group == 'age') {
                $arrears = Arrear::where('next_repayment_date', '=', $today)->whereRaw('(principal_arrears + outstanding_interest + next_repayment_principal + next_repayment_interest) !=0 ')->get()->groupBy(function ($arrear) {
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
                    } elseif ($age >= 151 && $age <= 180) {
                        return '151-180';
                    } else {
                        return '180+';
                    }
                });

                $groupKey = 'age';
                $nameField = null;
                $nameAttribute = null;
            } else if ($request->group == 'client') {
                $arrears = Arrear::where('next_repayment_date', '=', $today)
                    ->whereRaw('(principal_arrears + outstanding_interest + next_repayment_principal + next_repayment_interest) !=0 ')->get()->groupBy('customer_id');
                $groupKey = 'client_id';
                $nameField = 'customer';
                $nameAttribute = 'names';
            } else {
                // Default to group by staff_id
                $arrears = Arrear::where('next_repayment_date', '=', $today)->whereRaw('(principal_arrears + outstanding_interest + next_repayment_principal + next_repayment_interest) !=0 ')->get()->groupBy('staff_id');
                $groupKey = 'staff_id';
                $nameField = 'officer';
                $nameAttribute = 'names';
            }
        } else {
            // Default to group by staff_id if 'group' parameter is not provided
            $arrears = Arrear::where('next_repayment_date', '=', $today)->whereRaw('(principal_arrears + outstanding_interest + next_repayment_principal + next_repayment_interest) !=0 ')->get()->groupBy('staff_id');
            $groupKey = 'staff_id';
            $nameField = 'officer';
        }

        $previous_arrears = $this->group_previous_days($request);

        // Initialize data array
        $data = [];

        // Iterate through grouped arrears and calculate totals
        foreach ($previous_arrears as $key => $arrear) {
            //check if the key exists in the previous arrears and sum the $total_principal_arrears, $total_interest_arrears
            $previous_principal_arrears = 0;
            $previous_interest_arrears = 0;
            $previous_outstanding_principal = 0;
            $total_next_repayment_principal = 0;
            $total_next_repayment_interest = 0;
            $number_of_clients = 0;
            if (isset($arrears[$key])) {
                $previous_principal_arrears = $arrears[$key]->sum('principal_arrears');
                $previous_interest_arrears = $arrears[$key]->sum('outstanding_interest');
                $previous_outstanding_principal = $arrears[$key]->sum('outsanding_principal');
                $total_next_repayment_principal = $arrears[$key]->sum('next_repayment_principal');
                $total_next_repayment_interest = $arrears[$key]->sum('next_repayment_interest');
                $number_of_clients = $arrears[$key]->sum('number_of_group_members');
            }
            $present_principal_arrears = $arrear->sum('principal_arrears');
            $present_interest_arrears = $arrear->sum('outstanding_interest');
            $present_outstanding_principal = $arrear->sum('outsanding_principal');
            $total_principle_arrears = $present_principal_arrears + $previous_principal_arrears;
            $total_interest_arrears = $present_interest_arrears + $previous_interest_arrears;
            $total_outstanding_principal = $present_outstanding_principal + $previous_outstanding_principal;
            //remember that add column is named as interest_in_arrears
            $add = $arrear->sum('interest_in_arrears');
            $total_payment_amount = $total_next_repayment_principal + $total_next_repayment_interest + $total_principle_arrears + $total_interest_arrears;
            $expectedPrincipal = $total_principle_arrears + $total_next_repayment_principal;
            $expectedInterest = $total_interest_arrears + $total_next_repayment_interest;
            $expected_total = $expectedPrincipal + $expectedInterest;
            $clients_in_arrears = $arrear->where('number_of_days_late', '>', 0)->sum('number_of_group_members');
            $total_clients = $arrear->sum('number_of_group_members');
            $names = $arrear->first()->$nameField->$nameAttribute ?? "None"; // Fetch name based on grouping key
            $next_repayment_date = $arrear->first()->next_repayment_date;
            $phone_number = $arrear->first()->$nameField->phone ?? "None"; // Fetch name based on grouping key
            $number_of_comments = $arrear->first()->customer->comments->count();
            $amount_disbursed = $arrear->sum('amount_disbursed');
            $data[] = [
                'arrear_id' => $arrear->first()->id, // Fetch arrear id for the first record in the group
                'customer_id' => $arrear->first()->$nameField->customer_id ?? "None", // Fetch customer id for the first record in the group
                'group_key' => $key,
                'branch_id' => $arrear->first()->branch_id,
                'expected_principal' => $expectedPrincipal,
                'expected_interest' => $expectedInterest,
                'expected_total' => $expected_total ?? 0,
                'clients_in_arrears' => $clients_in_arrears,
                'total_clients' => $total_clients + $number_of_clients,
                'names' => $names,
                'next_repayment_date' => $next_repayment_date,
                'phone_number' => $phone_number,
                'number_of_comments' => $number_of_comments,
                'amount_disbursed' => $amount_disbursed,
                'total_outstanding_principal' => $total_outstanding_principal,
                'next_repayment_principal' => $total_next_repayment_principal,
                'next_repayment_interest' => $total_next_repayment_interest,
                'total_principle_arrears' => $total_principle_arrears,
                'total_interest_arrears' => $total_interest_arrears,
                'total_payment_amount' => $total_payment_amount,
                'number_of_days_late' => $arrear->first()->number_of_days_late,
                'add_per_customer' => $add,
                'previous_principal_in_arrears' => $previous_principal_arrears,
                'previous_interest_in_arrears' => $previous_interest_arrears,
                'previous_outstanding_principal' => $previous_outstanding_principal,
                'present_principal_in_arrears' => $present_principal_arrears,
                'present_interest_in_arrears' => $present_interest_arrears,
                'present_outstanding_principal' => $present_outstanding_principal,
            ];
        }

        //add those in arrears not in the previous arrears
        foreach ($arrears as $key => $arrear) {
            if (!isset($previous_arrears[$key])) {
                $present_principal_arrears = $arrear->sum('principal_arrears');
                $present_interest_arrears = $arrear->sum('outstanding_interest');
                $present_outstanding_principal = $arrear->sum('outsanding_principal');
                $total_next_repayment_principal = $arrear->sum('next_repayment_principal');
                $total_next_repayment_interest = $arrear->sum('next_repayment_interest');
                $total_principle_arrears = $present_principal_arrears;
                $total_interest_arrears = $present_interest_arrears + $previous_interest_arrears;
                $total_outstanding_principal = $present_outstanding_principal + $previous_outstanding_principal;
                //remember that add column is named as interest_in_arrears
                $add = $arrear->sum('interest_in_arrears');
                $total_payment_amount = $total_next_repayment_principal + $total_next_repayment_interest + $total_principle_arrears + $total_interest_arrears;
                $expectedPrincipal = $total_principle_arrears + $total_next_repayment_principal;
                $expectedInterest = $total_interest_arrears + $total_next_repayment_interest;
                $expected_total = $expectedPrincipal + $expectedInterest;
                $clients_in_arrears = $arrear->where('number_of_days_late', '>', 0)->sum('number_of_group_members');
                $total_clients = $arrear->sum('number_of_group_members');
                $names = $arrear->first()->$nameField->$nameAttribute ?? "None"; // Fetch name based on grouping key
                $next_repayment_date = $arrear->first()->next_repayment_date;
                $phone_number = $arrear->first()->$nameField->phone ?? "None"; // Fetch name based on grouping key
                $number_of_comments = $arrear->first()->customer->comments->count();
                $amount_disbursed = $arrear->sum('amount_disbursed');
                $data[] = [
                    'arrear_id' => $arrear->first()->id, // Fetch arrear id for the first record in the group
                    'customer_id' => $arrear->first()->$nameField->customer_id ?? "None", // Fetch customer id for the first record in the group
                    'group_key' => $key,
                    'branch_id' => $arrear->first()->branch_id,
                    'expected_principal' => $expectedPrincipal,
                    'expected_interest' => $expectedInterest,
                    'expected_total' => $expected_total ?? 0,
                    'clients_in_arrears' => $clients_in_arrears,
                    'total_clients' => $total_clients,
                    'names' => $names,
                    'next_repayment_date' => $next_repayment_date,
                    'phone_number' => $phone_number,
                    'number_of_comments' => $number_of_comments,
                    'amount_disbursed' => $amount_disbursed,
                    'total_outstanding_principal' => $total_outstanding_principal,
                    'next_repayment_principal' => $total_next_repayment_principal,
                    'next_repayment_interest' => $total_next_repayment_interest,
                    'total_principle_arrears' => $total_principle_arrears,
                    'total_interest_arrears' => $total_interest_arrears,
                    'total_payment_amount' => $total_payment_amount,
                    'number_of_days_late' => $arrear->first()->number_of_days_late,
                    'add_per_customer' => $add,
                    'previous_principal_in_arrears' => $previous_principal_arrears,
                    'previous_interest_in_arrears' => $previous_interest_arrears,
                    'previous_outstanding_principal' => $previous_outstanding_principal,
                    'present_principal_in_arrears' => $present_principal_arrears,
                    'present_interest_in_arrears' => $present_interest_arrears,
                    'present_outstanding_principal' => $present_outstanding_principal,
                ];

            }
        }

        // Return JSON response with data and success message
        return response()->json(['data' => $data, 'message' => 'success'], 200);
    }

    public function group_previous_days(Request $request)
    {
        $logged_in_user_type = auth()->user()->user_type;
        $today = date('j-M-y');
        // Check if request has group as parameter
        if ($request->has('group')) {
            if ($request->group == 'staff_id') {
                $arrears = Arrear::where('next_repayment_date', 'not =', $today)->whereRaw('(principal_arrears + outstanding_interest) !=0')->get()->groupBy('staff_id');
            } else if ($request->group == 'branch_id') {
                $arrears = Arrear::where('next_repayment_date', '!=', $today)->whereRaw('(principal_arrears + outstanding_interest) !=0 ')->get()->groupBy('branch_id');
            } else if ($request->group == 'region_id') {
                $arrears = Arrear::where('next_repayment_date', '!=', $today)->whereRaw('(principal_arrears + outstanding_interest) !=0 ')->get()->groupBy('region_id');
            } else if ($request->group == 'loan_product') {
                $arrears = Arrear::where('next_repayment_date', '!=', $today)->whereRaw('(principal_arrears + outstanding_interest) !=0 ')->get()->groupBy('product_id');
            } else if ($request->group == 'gender') {
                $arrears = Arrear::where('next_repayment_date', '!=', $today)->whereRaw('(principal_arrears + outstanding_interest) !=0 ')->get()->groupBy('gender');
            } else if ($request->group == 'district') {
                $arrears = Arrear::where('next_repayment_date', '!=', $today)->whereRaw('(principal_arrears + outstanding_interest) !=0 ')->get()->groupBy('district_id');
            } else if ($request->group == 'sub_county') {
                $arrears = Arrear::where('next_repayment_date', '!=', $today)->whereRaw('(principal_arrears + outstanding_interest) !=0 ')->get()->groupBy('subcounty_id');
            } else if ($request->group == 'village') {
                $arrears = Arrear::where('next_repayment_date', '!=', $today)->whereRaw('(principal_arrears + outstanding_interest) !=0 ')->get()->groupBy('village_id');
            } else if ($request->group == 'age') {
                $arrears = Arrear::where('next_repayment_date', '!=', $today)->whereRaw('(principal_arrears + outstanding_interest) !=0 ')->get()->groupBy(function ($arrear) {
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
                    } elseif ($age >= 151 && $age <= 180) {
                        return '151-180';
                    } else {
                        return '180+';
                    }
                });
            } else if ($request->group == 'client') {
                $arrears = Arrear::where('next_repayment_date', '!=', $today)->whereRaw('(principal_arrears + outstanding_interest) !=0 ')->get()->groupBy('customer_id');
            } else {
                // Default to group by staff_id
                $arrears = Arrear::where('next_repayment_date', '!=', $today)->whereRaw('(principal_arrears + outstanding_interest) !=0 ')->get()->groupBy('staff_id');
            }
        } else {
            // Default to group by staff_id if 'group' parameter is not provided
            $arrears = Arrear::where('next_repayment_date', '!=', $today)->whereRaw('(principal_arrears + outstanding_interest) !=0 ')->get()->groupBy('staff_id');
        }

        return $arrears;
    }
}
