<?php

namespace App\Http\Controllers;

use App\Models\Arrear;
use App\Models\Officer;
use Illuminate\Support\Facades\DB;

class IncentiveController extends Controller
{
    public function index()
    {

        return view('incentives');
    }

    public function getAllTheIncentives()
    {
        $incentives = $this->calculateIncentives();
        $incentivesWithDetails = [];
        $logged_user = auth()->user()->user_type;
        $staff_id = auth()->user()->staff_id;

        if ($logged_user == 1) {
            foreach ($incentives as $staffId => $incentive) {
                // Get staff_id details from officers table
                $officer = Officer::where('staff_id', $staffId)->first();

                // Combine the officer details with the incentives
                $incentivesWithDetails[$staffId] = [
                    'incentive' => $incentive,
                    'officer_details' => $officer,
                ];
            }
        } else {
            foreach ($incentives as $staffId => $incentive) {
                // Get staff_id details from officers table
                if ($staffId==$staff_id) {
                    $officer = Officer::where('staff_id', $staffId)->first();

                    // Combine the officer details with the incentives
                    $incentivesWithDetails[$staffId] = [
                        'incentive' => $incentive,
                        'officer_details' => $officer,
                    ];

                    //stop the loop if the staff_id is equal to the logged in user staff_id
                    break;
                }
            }
        }
        return response()->json(['incentives' => $incentivesWithDetails], 200);

    }

    public function calculateIncentives()
    {
        // Initialize an array to store results for each staff ID
        $incentives = [];

        // Retrieve results for individual staff IDs
        $outstandingPrincipalIndividual = $this->calculateOutstandingPrincipalIndividual();
        $uniqueCustomerIDIndividual = $this->calculateUniqueCustomerIDIndividual();
        $recordsForPAR = $this->recordsForPAR();
        $monthlyLoanLossRate = $this->recordsForMonthlyLoanLossRate();

        // Retrieve results for group staff IDs
        $outstandingPrincipalGroup = $this->calculateOutstandingPrincipalGroup();
        $recordsForUniqueGroupIDGroup = $this->recordsForUniqueGroupIDGroup();
        $recordsForNoOfGroups = $this->recordsForNoOfGroups();

        // Merge results for each staff ID
        foreach ($outstandingPrincipalIndividual as $record) {
            $staffId = $record->staff_id;
            $incentives[$staffId] = [
                'outstanding_principal_individual' => $record->count,
            ];
        }

        foreach ($uniqueCustomerIDIndividual as $record) {
            $staffId = $record->staff_id;
            if (!isset($incentives[$staffId])) {
                $incentives[$staffId] = [];
            }
            $incentives[$staffId]['unique_customer_id_individual'] = $record->count;
        }

        foreach ($recordsForPAR as $record) {
            $staffId = $record->staff_id;
            if (!isset($incentives[$staffId])) {
                $incentives[$staffId] = [];
            }
            $incentives[$staffId]['records_for_PAR'] = $record->count;
        }

        foreach ($monthlyLoanLossRate as $record) {
            $staffId = $record->staff_id;
            if (!isset($incentives[$staffId])) {
                $incentives[$staffId] = [];
            }
            $incentives[$staffId]['monthly_loan_loss_rate'] = $record->count;
        }

        foreach ($outstandingPrincipalGroup as $record) {
            $staffId = $record->staff_id;
            if (!isset($incentives[$staffId])) {
                $incentives[$staffId] = [];
            }
            $incentives[$staffId]['outstanding_principal_group'] = $record->count;
        }

        foreach ($recordsForUniqueGroupIDGroup as $record) {
            $staffId = $record->staff_id;
            if (!isset($incentives[$staffId])) {
                $incentives[$staffId] = [];
            }
            $incentives[$staffId]['records_for_unique_group_id_group'] = $record->count;
        }

        foreach ($recordsForNoOfGroups as $record) {
            $staffId = $record->staff_id;
            if (!isset($incentives[$staffId])) {
                $incentives[$staffId] = [];
            }
            $incentives[$staffId]['sgl_records'] = $record->count;
        }

        //filter only those with sgl_records property or has all [outstanding_principal_individual, unique_customer_id_individual, records_for_PAR, monthly_loan_loss_rate, outstanding_principal_group,  records_for_unique_group_id_group]
        $incentives = array_filter($incentives, function ($incentive) {
            return isset($incentive['sgl_records']) || (isset($incentive['outstanding_principal_individual']) && isset($incentive['unique_customer_id_individual']) && isset($incentive['records_for_PAR']) && isset($incentive['monthly_loan_loss_rate']) && isset($incentive['outstanding_principal_group']) && isset($incentive['records_for_unique_group_id_group']));
        });

        return $incentives;
    }

    //parameter 1
    public function calculateOutstandingPrincipalIndividual()
    {
        $outstandingPrincipalSumIndividual = Arrear::select('staff_id', DB::raw('SUM(outsanding_principal) as count'))
            ->where('lending_type', 'Individual')
            ->groupBy('staff_id')
            ->havingRaw('SUM(outsanding_principal) > 130000000') // Filter the sum
            ->get();

        return $outstandingPrincipalSumIndividual;
    }

    //parameter 2
    public function calculateOutstandingPrincipalGroup()
    {
        $outstandingPrincipalSumGroup = Arrear::select('staff_id', DB::raw('SUM(outsanding_principal) as count'))
            ->where('lending_type', 'Group')
            ->groupBy('staff_id')
            ->havingRaw('SUM(outsanding_principal) > 90000000') // Filter the sum
            ->get();

        return $outstandingPrincipalSumGroup;
    }

    //parameter 3
    public function calculateUniqueCustomerIDIndividual()
    {
        //group by staff_id by calculating the number of unique customer_id
        $uniqueCustomerIDIndividual = Arrear::select('staff_id', DB::raw('COUNT(DISTINCT customer_id) as count'))
            ->where('lending_type', 'Individual')
            ->groupBy('staff_id')
            ->havingRaw('COUNT(DISTINCT customer_id) > 130') // Filter the count
            ->get();

        return $uniqueCustomerIDIndividual;
    }

    //parameter 4
    public function recordsForUniqueGroupIDGroup()
    {
        //group by staff_id by calculating the number of unique group_id
        $uniqueGroupIDGroup = Arrear::select('staff_id', DB::raw('COUNT(DISTINCT group_id) as count'))
            ->where('lending_type', 'Group')
            ->groupBy('staff_id')
            ->havingRaw('COUNT(DISTINCT group_id) > 140') // Filter the count
            ->get();

        return $uniqueGroupIDGroup;
    }

    //parameter 5
    public function recordsForPAR()
    {
        // Retrieve staff_id and PAR percentage directly from raw SQL query, rounded to 1 decimal place
        $recordsForPAR = Arrear::selectRaw('staff_id,
                                             ROUND(SUM(par) / SUM(outsanding_principal) * 100, 1) as count')
            ->whereRaw('(product_id != 21070)') // Exclude product ID 21070
            ->groupBy('staff_id')
            ->havingRaw('ROUND((SUM(par) / SUM(outsanding_principal) * 100), 1) >= 6.5')
            ->get();

        return $recordsForPAR;
    }

    //parameter 6
    public function recordsForMonthlyLoanLossRate()
    {
        // Calculate the monthly loan loss rate for each staff
        $monthlyLoanLossRate = Arrear::selectRaw('staff_id,
            round((SUM(CASE WHEN number_of_days_late > 180 THEN outsanding_principal ELSE 0 END) /
             SUM(outsanding_principal)) * 100, 2) as count')
            ->where('product_id', '!=', '21070')
            ->groupBy('staff_id')
            ->havingRaw('count < 0.18')
            ->get();

        return $monthlyLoanLossRate;
    }
    //parameter 7
    public function recordsForNoOfGroups()
    {
        $noOfGroups = Arrear::select('staff_id', DB::raw('COUNT(customer_id) as count'))
            ->where('product_id', '21070')
            ->groupBy('staff_id')
            ->havingRaw('COUNT(customer_id) >= 30')
            ->get();

        return $noOfGroups;
    }

}
