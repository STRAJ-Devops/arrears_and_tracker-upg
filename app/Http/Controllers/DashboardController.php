<?php

namespace App\Http\Controllers;

use App\Models\Arrear;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $logged_user = auth()->user()->user_type;
        $staff_id = auth()->user()->staff_id;
        $outstanding_principal = $logged_user==1?Arrear::sum('outsanding_principal'): Arrear::where('staff_id', $staff_id)->sum('outsanding_principal');

        $outstanding_interest = $logged_user==1?Arrear::sum('outstanding_interest'): Arrear::where('staff_id', $staff_id)->sum('outstanding_interest');

        $principal_arrears = $logged_user==1?Arrear::sum('principal_arrears'):  Arrear::where('staff_id', $staff_id)->sum('principal_arrears');

        $number_of_female_borrowers = $logged_user==1?Sale::where('gender', 'female')->count(): Sale::where('gender', 'female')->where('staff_id', $staff_id)->count();

        $number_of_children = $logged_user==1?Sale::sum('number_of_children'): Sale::where('staff_id', $staff_id)->sum('number_of_children');

        $total_disbursements_this_month = $logged_user==1?Sale::whereMonth('disbursement_date', date('m'))->sum('disbursement_amount'): Sale::where('staff_id', $staff_id)->whereMonth('disbursement_date', date('m'))->sum('disbursement_amount');

        //get par 30 days that is sum of par for all arrears that are more than 30 days late
        $par_30_days = $logged_user==1?Arrear::where('number_of_days_late', '>', 30)->sum('par'): Arrear::where('staff_id', $staff_id)->where('number_of_days_late', '>', 30)->sum('par');

        $par_30_per = $outstanding_principal == 0 ? 0 : (($par_30_days / $outstanding_principal) * 100);

        //get pa 1 day that is sum of par for all arrears that are more than 1 day late
        $par_1_days = $logged_user==1?Arrear::where('number_of_days_late', '>', 1)->sum('par'): Arrear::where('staff_id', $staff_id)->where('number_of_days_late', '>', 1)->sum('par');

        $par_1_per = $outstanding_principal == 0 ? 0 : (($par_1_days / $outstanding_principal) * 100);

        //create an array called pie_array for principal arrears and outstanding principal
        $pie_data = [
            'principal_arrears' => $principal_arrears,
            'outstanding_principal' => $outstanding_principal,
        ];

        $data = [
            'outstanding_principal' => $outstanding_principal,
            'outstanding_interest' => $outstanding_interest,
            'principal_arrears' => $principal_arrears,
            'number_of_female_borrowers' => $number_of_female_borrowers,
            'number_of_children' => $number_of_children,
            'total_disbursements' => $total_disbursements_this_month,
            'par_30_days' => round($par_30_per),
            'par_1_days' => round($par_1_per),
            'pie_array' => $pie_data,
        ];

        return view('dashboard', compact('data'));
    }
}
