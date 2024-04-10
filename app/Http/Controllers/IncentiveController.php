<?php

namespace App\Http\Controllers;

use App\Models\Arrear;
use App\Models\Officer;
use App\Models\PreviousEndMonth;
use Illuminate\Support\Facades\DB;

class IncentiveController extends Controller
{
    public function index()
    {
        $logged_user = auth()->user()->user_type;
        return view('incentives', compact('logged_user'));
    }

    public function calculateIncentive()
    {
        $incentives = $this->getAllIncentives();
        $incentivesWithDetails = [];
        $logged_user = 5;
        $staff_id = 1106;

        if ($logged_user == 5) {
            foreach ($incentives as $staffId => $value) {
                // Get staff_id details from officers table
                $officer = Officer::where('staff_id', $staffId)->first();

                //incentive amount for PAR
                $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($value['records_for_PAR']);
                //incentive amount for Net Portfolio Growth
                $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($value['net_portifolio_growth']);

                //incentive amount for Net Client Growth
                $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($value['net_client_growth']);

                //total incentive amount
                $incentive['total_incentive_amount'] =ROUND(($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_amount_Net_Client_Growth']), 2);

                // Combine the officer details with the incentives
                $incentivesWithDetails[$staffId] = [
                    'incentive' => $incentive,
                    'officer_details' => $officer,
                ];
            }
        } else {
            foreach ($incentives as $staffId => $incentive) {
                // Get staff_id details from officers table
                if ($staffId == $staff_id) {
                    $officer = Officer::where('staff_id', $staffId)->first();

                    // $incentive['outstanding_principal_individual'] = Arrear::where('staff_id', $staffId)->where('lending_type', 'Individual')->sum('outsanding_principal');
                    // $incentive['outstanding_principal_group'] = Arrear::where('staff_id', $staffId)->where('lending_type', 'Group')->sum('outsanding_principal');
                    // $incentive['unique_customer_id_individual'] = Arrear::where('staff_id', $staffId)->where('lending_type', 'Individual')->distinct('customer_id')->count('customer_id');
                    // $incentive['records_for_unique_group_id_group'] = Arrear::where('staff_id', $staffId)->where('lending_type', 'Group')->distinct('group_id')->count('group_id');
                    // $incentive['records_for_PAR'] = Arrear::where('staff_id', $staffId)->where('product_id', '!=', '21070')->sum('par');
                    // $incentive['monthly_loan_loss_rate'] = Arrear::where('staff_id', $staffId)->where('product_id', '!=', '21070')->sum('number_of_days_late');
                    // $incentive['sgl_records'] = Arrear::where('staff_id', $staffId)->where('product_id', '21070')->count('customer_id');

                    //incentive amount for PAR
                    $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR']);

                    //incentive amount for Net Portfolio Growth
                    $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['outstanding_principal_individual']);

                    //incentive amount for Net Client Growth
                    $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($incentive['net_client_growth']);

                    //total incentive amount
                    $incentive['total_incentive_amount'] = $incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_amount_Net_Client_Growth'];

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

    public function getAllIncentives()
    {
        $overallIndividualRecords = $this->overallIndividualRecords();
        $overallGroupRecords = $this->overallGroupRecords();
        // $overallSGLRecords = $this->overallSGLRecords();

        $incentives = [];

        foreach ($overallIndividualRecords as $staffId => $record) {
            /**
             * add net portifolio growth and net client growth to the record
             * from PreviousEndMonth Model
             */
            $previousMonthOutstandingPrincipal = PreviousEndMonth::where('staff_id', $staffId)->sum('outsanding_principal');
            $record['previous_outstanding_principal_individual'] = $previousMonthOutstandingPrincipal;
            $record['outstanding_principal_group'] = 0;
            $record['records_for_unique_group_id_group'] = 0;
            $record['sgl_records'] = 0;
            $netPortifolioGrowth = $this->calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $record['outstanding_principal_individual']);
            $record['net_portifolio_growth'] = $netPortifolioGrowth;

            $previousMonthUniqueCustomerCount = PreviousEndMonth::where('staff_id', $staffId)
                ->where('lending_type', 'Individual')
                ->distinct('customer_id')
                ->count('customer_id');

            $netClientGrowth = $this->calculateNetClientGrowth($previousMonthUniqueCustomerCount, $record['unique_customer_id_individual']);
            $record['net_client_growth'] = $netClientGrowth;

            $incentives[$staffId] = $record;
        }

        foreach ($overallGroupRecords as $staffId => $record) {
            /**
             * add net portifolio growth and net client growth to the record
             * from PreviousEndMonth Model
             */
            $previousMonthOutstandingPrincipal = PreviousEndMonth::where('staff_id', $staffId)->sum('outsanding_principal');
            $record['previous_outstanding_principal_group'] = $previousMonthOutstandingPrincipal;
            $record['outstanding_principal_individual'] = 0;
            $record['unique_customer_id_individual'] = 0;
            $record['records_for_PAR'] = 0;
            $netPortifolioGrowth = $this->calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $record['outstanding_principal_group']);
            $record['net_portifolio_growth'] = $netPortifolioGrowth;

            $previousMonthUniqueCustomerCount = PreviousEndMonth::where('staff_id', $staffId)
                ->where('lending_type', 'Group')
                ->distinct('group_id')
                ->count('group_id');

            $netClientGrowth = $this->calculateNetClientGrowth($previousMonthUniqueCustomerCount, $record['records_for_unique_group_id_group']);

            $record['net_client_growth'] = $netClientGrowth;
            $incentives[$staffId] = $record;
        }

        // foreach ($overallSGLRecords as $staffId => $record) {
        //     $incentives[$staffId] = $record;
        // }

        return $incentives;
    }

    /**
     * Individual client incentive parameters
     */
    public function calculateOutstandingPrincipalIndividual()
    {
        $outstandingPrincipalSumIndividual = Arrear::select('staff_id', DB::raw('SUM(outsanding_principal) as count'))
            ->where('lending_type', 'Individual')
            ->groupBy('staff_id')
            ->havingRaw('SUM(outsanding_principal) >= 130000000') // Filter the sum
            ->get();

        return $outstandingPrincipalSumIndividual;
    }

    //parameter 3
    public function calculateUniqueCustomerIDIndividual()
    {
        //group by staff_id by calculating the number of unique customer_id
        $uniqueCustomerIDIndividual = Arrear::select('staff_id', DB::raw('COUNT(DISTINCT customer_id) as count'))
            ->where('lending_type', 'Individual')
            ->groupBy('staff_id')
            ->havingRaw('COUNT(DISTINCT customer_id) >= 130') // Filter the count
            ->get();

        return $uniqueCustomerIDIndividual;
    }

    /**
     * Group client incentive parameters
     */

    //outstanding principal for group
    public function calculateOutstandingPrincipalGroup()
    {
        $outstandingPrincipalSumGroup = Arrear::select('staff_id', DB::raw('SUM(outsanding_principal) as count'))
            ->where('lending_type', 'Group')
            ->groupBy('staff_id')
            ->havingRaw('SUM(outsanding_principal) >= 90000000') // Filter the sum
            ->get();

        return $outstandingPrincipalSumGroup;
    }

    //number of total customers in a groups
    public function recordsForUniqueGroupIDGroup()
    {
        //group by staff_id by calculating the number of unique group_id
        $uniqueGroupIDGroup = Arrear::select('staff_id', DB::raw('COUNT(DISTINCT group_id) as count'))
            ->where('lending_type', 'Group')
            ->groupBy('staff_id')
            ->havingRaw('COUNT(DISTINCT group_id) >= 140') // Filter the count
            ->get();

        return $uniqueGroupIDGroup;
    }

    //par per officer
    public function recordsForPAR()
    {
        // Retrieve staff_id and PAR percentage directly from raw SQL query, rounded to 1 decimal place
        $recordsForPAR = Arrear::selectRaw('staff_id, ROUND(SUM(par) / SUM(outsanding_principal) * 100, 2) as count')
            ->whereRaw('(product_id != 21070)') // Exclude product ID 21070
            ->groupBy('staff_id')
            ->havingRaw('ROUND((SUM(par) / SUM(outsanding_principal) * 100), 1) <= 6.5')
            ->get();

        return $recordsForPAR;
    }

    //llr per officer
    public function recordsForMonthlyLoanLossRate()
    {
        // Calculate the monthly loan loss rate for each staff
        $monthlyLoanLossRate = Arrear::selectRaw('staff_id,
            round((SUM(CASE WHEN number_of_days_late > 180 THEN outsanding_principal ELSE 0 END) /
             SUM(outsanding_principal)) * 100, 2) as count')
            ->where('product_id', '!=', '21070')
            ->groupBy('staff_id')
            ->havingRaw('count <= 0.18')
            ->get();

        return $monthlyLoanLossRate;
    }
    /**
     * records for officers meeting the criteria for individual clients in the order
     * calculateOutstandingPrincipalIndividual
     * calculateUniqueCustomerIDIndividual
     * recordsForPAR
     * recordsForMonthlyLoanLossRate
     */
    public function overallIndividualRecords()
    {
        $outstandingPrincipalIndividual = $this->calculateOutstandingPrincipalIndividual();
        $uniqueCustomerIDIndividual = $this->calculateUniqueCustomerIDIndividual();
        $recordsForPAR = $this->recordsForPAR();
        $monthlyLoanLossRate = $this->recordsForMonthlyLoanLossRate();

        $overallIndividualRecords = [];

        foreach ($outstandingPrincipalIndividual as $record) {
            $staffId = $record->staff_id;
            $overallIndividualRecords[$staffId] = [
                'outstanding_principal_individual' => $record->count,
            ];
        }

        foreach ($uniqueCustomerIDIndividual as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallIndividualRecords[$staffId])) {
                $overallIndividualRecords[$staffId] = [];
            }
            $overallIndividualRecords[$staffId]['unique_customer_id_individual'] = $record->count;
        }

        foreach ($recordsForPAR as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallIndividualRecords[$staffId])) {
                $overallIndividualRecords[$staffId] = [];
            }
            $overallIndividualRecords[$staffId]['records_for_PAR'] = $record->count;
        }

        foreach ($monthlyLoanLossRate as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallIndividualRecords[$staffId])) {
                $overallIndividualRecords[$staffId] = [];
            }
            $overallIndividualRecords[$staffId]['monthly_loan_loss_rate'] = $record->count;
        }

        //filter only those with sgl_records property or has all [outstanding_principal_individual, unique_customer_id_individual, records_for_PAR, monthly_loan_loss_rate]
        $overallIndividualRecords = array_filter($overallIndividualRecords, function ($record) {
            return isset($record['outstanding_principal_individual']) && isset($record['unique_customer_id_individual']) && isset($record['records_for_PAR']) && isset($record['monthly_loan_loss_rate']);
        });

        return $overallIndividualRecords;
    }

    /**
     * records for officers meeting the criteria for group clients in the order
     * calculateOutstandingPrincipalGroup
     * recordsForUniqueGroupIDGroup
     * recordsForPAR
     * recordsForMonthlyLoanLossRate
     */
    public function overallGroupRecords()
    {
        $outstandingPrincipalGroup = $this->calculateOutstandingPrincipalGroup();
        $recordsForUniqueGroupIDGroup = $this->recordsForUniqueGroupIDGroup();
        $recordsForPAR = $this->recordsForPAR();
        $monthlyLoanLossRate = $this->recordsForMonthlyLoanLossRate();

        $overallGroupRecords = [];

        foreach ($outstandingPrincipalGroup as $record) {
            $staffId = $record->staff_id;
            $overallGroupRecords[$staffId] = [
                'outstanding_principal_group' => $record->count,
            ];
        }

        foreach ($recordsForUniqueGroupIDGroup as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallGroupRecords[$staffId])) {
                $overallGroupRecords[$staffId] = [];
            }
            $overallGroupRecords[$staffId]['records_for_unique_group_id_group'] = $record->count;
        }

        foreach ($recordsForPAR as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallGroupRecords[$staffId])) {
                $overallGroupRecords[$staffId] = [];
            }
            $overallGroupRecords[$staffId]['records_for_PAR'] = $record->count;
        }

        foreach ($monthlyLoanLossRate as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallGroupRecords[$staffId])) {
                $overallGroupRecords[$staffId] = [];
            }
            $overallGroupRecords[$staffId]['monthly_loan_loss_rate'] = $record->count;
        }

        //filter only those with sgl_records property or has all [outstanding_principal_group, records_for_unique_group_id_group, records_for_PAR, monthly_loan_loss_rate]
        $overallGroupRecords = array_filter($overallGroupRecords, function ($record) {
            return isset($record['outstanding_principal_group']) && isset($record['records_for_unique_group_id_group']) && isset($record['records_for_PAR']) && isset($record['monthly_loan_loss_rate']);
        });

        return $overallGroupRecords;
    }

    /**
     * SGL Incentive parameters
     */

    //number of groups PAR
    public function recordsForNoOfGroupsPAR()
    {
        // Retrieve staff_id and PAR percentage directly from raw SQL query, rounded to 1 decimal place
        $recordsForPAR = Arrear::selectRaw('staff_id, ROUND(SUM(par) / SUM(outsanding_principal) * 100, 2) as count')
            ->whereRaw('(product_id = 21070)') // Exclude product ID 21070
            ->groupBy('staff_id')
            ->havingRaw('ROUND((SUM(par) / SUM(outsanding_principal) * 100), 1) <= 6.5')
            ->get();

        return $recordsForPAR;
    }

    //number of groups LLR
    public function recordsForMonthlyLoanLossRateGroup()
    {
        // Calculate the monthly loan loss rate for each staff
        $monthlyLoanLossRate = Arrear::selectRaw('staff_id,
            round((SUM(CASE WHEN number_of_days_late > 180 THEN outsanding_principal ELSE 0 END) /
             SUM(outsanding_principal)) * 100, 2) as count')
            ->where('product_id', '=', '21070')
            ->groupBy('staff_id')
            ->havingRaw('count <= 0.18')
            ->get();

        return $monthlyLoanLossRate;
    }

    //number of groups customer
    public function recordsForNoOfGroupCustomer()
    {
        $noOfGroups = Arrear::selectRaw('staff_id', DB::raw('COUNT(customer_id) as count'))
            ->where('product_id', '21070')
            ->groupBy('staff_id')
            ->havingRaw('COUNT(customer_id) >= 30')
            ->get();

        return $noOfGroups;
    }

    /**
     * overall SGL records meeting criteria in the order
     * recordsForNoOfGroupsPAR
     * recordsForMonthlyLoanLossRateGroup
     * recordsForNoOfGroupCustomer
     * by merging the results, we can get the staff_id that meets all the criteria
     */
    public function overallSGLRecords()
    {
        $recordsForNoOfGroupsPAR = $this->recordsForNoOfGroupsPAR();
        $recordsForMonthlyLoanLossRateGroup = $this->recordsForMonthlyLoanLossRateGroup();
        $recordsForNoOfGroupCustomer = $this->recordsForNoOfGroupCustomer();

        $overallSGLRecords = [];

        foreach ($recordsForNoOfGroupsPAR as $record) {
            $staffId = $record->staff_id;
            $overallSGLRecords[$staffId] = [
                'recordsForNoOfGroupsPAR' => $record->count,
            ];
        }

        foreach ($recordsForMonthlyLoanLossRateGroup as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallSGLRecords[$staffId])) {
                $overallSGLRecords[$staffId] = [];
            }
            $overallSGLRecords[$staffId]['recordsForMonthlyLoanLossRateGroup'] = $record->count;
        }

        foreach ($recordsForNoOfGroupCustomer as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallSGLRecords[$staffId])) {
                $overallSGLRecords[$staffId] = [];
            }
            $overallSGLRecords[$staffId]['recordsForNoOfGroupCustomer'] = $record->count;
        }

        //filter only those with sgl_records property or has all [recordsForNoOfGroupsPAR, recordsForMonthlyLoanLossRateGroup, recordsForNoOfGroupCustomer]
        $overallSGLRecords = array_filter($overallSGLRecords, function ($record) {
            return isset($record['recordsForNoOfGroupsPAR']) && isset($record['recordsForMonthlyLoanLossRateGroup']) && isset($record['recordsForNoOfGroupCustomer']);
        });

        return $overallSGLRecords;
    }

    /**
     * get the net portifolio growth
     * using previous month outstanding principal and current month outstanding principal
     */
    public function calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $currentMonthOutstandingPrincipal)
    {
        $netPortifolioGrowth = $currentMonthOutstandingPrincipal - $previousMonthOutstandingPrincipal;
        return $netPortifolioGrowth;
    }

    /**
     * get the net client growth
     * using previous month unique customer id and current month unique customer id
     */
    public function calculateNetClientGrowth($previousMonthUniqueCustomerID, $currentMonthUniqueCustomerID)
    {
        $netClientGrowth = $currentMonthUniqueCustomerID - $previousMonthUniqueCustomerID;
        return $netClientGrowth;
    }

    /**
     * Incentive calculations
     */

    public function calculateIncentiveAmountPAR($par)
    {
        $amount = 0;
        if (($par/100) <= (6.5/100)) {
            $amount = (((6.5/100) - ($par/100)) / (6.5/100)) * (20 / 100) * 500000;
        }

        return ROUND($amount, 2);
    }

    public function calculateIncentiveAmountNetPortifolioGrowth($outstandingPrincipalIndividual)
    {
        $max = 40000000;
        $min = 5000000;
        $actual = $outstandingPrincipalIndividual;
        $amount = 0;

        //if $actual is less than  50000000
        if (($actual > $min) && ($actual < $max)) {
            $amount = (ROUND(($actual - $min) / ($max - $min), 2)) * (40 / 100) * 500000;
        }
        //greater than 40000000
        if ($actual >= $max) {
            $amount = (($max) / ($max - $min)) * (40 / 100) * 500000;
        }

        return ROUND($amount, 2);
    }

    public function calculateIncentiveAmountNetClientGrowth($uniqueCustomerIDIndividual)
    {
        $max = 20;
        $min = 5;

        $actual = $uniqueCustomerIDIndividual;
        $amount = 0;

        if ($actual >= 5) {
            $amount = (($actual - $min) / ($max - $min)) * (40 / 100) * 500000;
        }

        //if $actual is greater than 20
        if ($actual >= 20) {
            $amount = (($max) / ($max - $min)) * (40 / 100) * 500000;
        }

        return ROUND($amount, 2);
    }

}
