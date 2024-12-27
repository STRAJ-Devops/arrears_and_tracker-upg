<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Arrear;
use Illuminate\Http\Request;

class ArrearController extends Controller
{
    public function group_by(Request $request)
    {
        // Check if request has group as parameter
        if ($request->has('group')) {
            if ($request->group == 'staff_id') {
                $arrears = Arrear::all()->groupBy('staff_id');
                $groupKey = 'staff_id';
                $nameField = 'officer';
                $nameAttribute = 'names';
            } else if ($request->group == 'branch_id') {
                $arrears = Arrear::all()->groupBy('branch_id');
                $groupKey = 'branch_id';
                $nameField = 'branch';
                $nameAttribute = 'branch_name';
            } else if ($request->group == 'region_id') {
                $arrears = Arrear::all()->groupBy('region_id');
                $groupKey = 'region_id';
                $nameField = 'region';
                $nameAttribute = 'region_name';
            } else if ($request->group == 'loan_product') {
                $arrears = Arrear::all()->groupBy('product_id');
                $groupKey = 'product_id';
                $nameField = 'product';
                $nameAttribute = 'product_name';
            } else if ($request->group == 'gender') {
                $arrears = Arrear::all()->groupBy('gender');
                $groupKey = 'gender';
                $nameField = 'gender';
                $nameAttribute = "None";
            } else if ($request->group == 'district') {
                $arrears = Arrear::all()->groupBy('district_id');
                $groupKey = 'district_id';
                $nameField = 'district';
                $nameAttribute = 'district_name';
            } else if ($request->group == 'sub_county') {
                $arrears = Arrear::all()->groupBy('subcounty_id');
                $groupKey = 'subcounty_id';
                $nameField = 'sub_county';
                $nameAttribute = 'subcounty_name';
            } else if ($request->group == 'village') {
                $arrears = Arrear::all()->groupBy('village_id');
                $groupKey = 'village_id';
                $nameField = 'village';
                $nameAttribute = 'village_name';
            } else if ($request->group == 'age') {
                $arrears = Arrear::where('number_of_days_late', '>', '0')->get()->groupBy(function ($arrear) {
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
                // $arrears = Arrear::where("staff_id", 1050)->get()->groupBy('customer_id');
                $arrears = Arrear::whereRaw('(principal_arrears+outstanding_interest)>0')->get()->groupBy('customer_id');
                $groupKey = 'client_id';
                $nameField = 'customer';
                $nameAttribute = 'names';
            } else {
                // Default to group by staff_id
                $arrears = Arrear::all()->groupBy('staff_id');
                $groupKey = 'staff_id';
                $nameField = 'officer';
                $nameAttribute = 'names';
            }
        } else {
            // Default to group by staff_id if 'group' parameter is not provided
            $arrears = Arrear::all()->groupBy('staff_id');
            $groupKey = 'staff_id';
            $nameField = 'officer';
        }

        // Initialize data array
        $data = [];

        // Iterate through grouped arrears and calculate totals
        foreach ($arrears as $key => $arrear) {

            $total_principle_arrears = $arrear->sum('principal_arrears');
            $total_outstanding_principal = $arrear->sum('outsanding_principal');
            //remember that interest in arrears is stored as outstanding_interest
            $total_interest_arrears = $arrear->sum('outstanding_interest');
            $total_arrears = $total_principle_arrears + $total_interest_arrears;
            $clients_in_arrears = $arrear->where('number_of_days_late', '>', 0)->count();
            $total_clients = $arrear->sum('number_of_group_members');
            $names = $arrear->first()->$nameField->$nameAttribute ?? "None"; // Fetch name based on grouping key
            $total_par = $total_outstanding_principal != 0 ? (($arrear->sum('par') / $total_outstanding_principal) * 100) : 0;
            $phone_number = $arrear->first()->$nameField->phone ?? "None"; // Fetch name based on grouping key
            $number_of_comments = $arrear->first()->customer->comments->count();
            $amount_disbursed = $arrear->sum('amount_disbursed');
            $branch_name = $arrear->first()->branch->branch_name ?? "None";
            $number_of_days_late = $arrear->sum('number_of_days_late');

            $data[] = [
                'arrear_id' => $arrear->first()->id, // Fetch arrear id for the first record in the group
                'customer_id' => $arrear->first()->customer->customer_id, // Fetch customer id for the first record in the group
                'group_key' => $key,
                'total_principle_arrears' => $total_principle_arrears,
                'total_interest_arrears' => $total_interest_arrears,
                'total_arrears' => $total_arrears,
                'clients_in_arrears' => $clients_in_arrears,
                'total_clients' => $total_clients,
                'names' => $names,
                'branch_name' => $branch_name,
                'total_par' => number_format(round($total_par, 2), 2),
                'phone_number' => $phone_number,
                'number_of_comments' => $number_of_comments,
                'amount_disbursed' => $amount_disbursed,
                'total_outstanding_principal' => $total_outstanding_principal,
                'number_of_days_late' => $number_of_days_late,
            ];
        }

        // Return JSON response with data and success message
        return response()->json(['data' => $data, 'message' => 'success'], 200);
    }
}
