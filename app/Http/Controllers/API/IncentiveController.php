<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Arrear;
use App\Models\IncentiveSettings;
use App\Models\Officer;
use App\Models\PreviousEndMonth;
use App\Models\Scopes\ArrearScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncentiveController extends Controller
{
    public function calculateIncentive()
    {
        $incentives = $this->getAllIncentives();
        $incentivesWithDetails = [];
        $logged_user = auth()->user()->user_type;
        $staff_id = auth()->user()->staff_id;

        if ($logged_user == 5 || $logged_user == 4) {
            foreach ($incentives as $staffId => $incentive) {
                // Get staff_id details from officers table
                $officer = Officer::where('staff_id', $staffId)->first();

                //check if officer branch_id is 1000 and just continue. this is to eliminate head office staff
                if ($officer->branch_id == 1000) {
                    continue;
                }
                if ($this->determineQualifiers($incentive)) {
                    if (array_key_exists('outstanding_principal_sgl', $incentive)) {
                        //incentive amount for PAR
                        $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPARSGL($incentive['records_for_PAR']);
                        //incentive amount for Net Portfolio Growth
                        $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowthSGL($incentive['net_portifolio_growth']);
                        //incentive amount for Net Client Growth
                        $incentive['incentive_number_of_sgl_groups'] = $this->calculateIncentiveAmountSGLGroups($incentive['sgl_records']);
                        //total incentive amount
                        $incentive['total_incentive_amount'] = ROUND(($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_number_of_sgl_groups']), 2);
                    } else {
                        //incentive amount for PAR
                        $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR']);
                        //incentive amount for Net Portfolio Growth
                        $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['net_portifolio_growth']);

                        //incentive amount for Net Client Growth
                        $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($incentive['net_client_growth']);

                        $incentives['sgl_records'] = 0;

                        //total incentive amount
                        $incentive['total_incentive_amount'] = ROUND(($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_amount_Net_Client_Growth']), 2);
                    }
                } else {
                    $incentive['incentive_amount_PAR'] = 0;
                    $incentive['incentive_amount_Net_Portifolio_Growth'] = 0;
                    $incentive['incentive_amount_Net_Client_Growth'] = 0;
                    $incentive['total_incentive_amount'] = 0;
                    $incentives['sgl_records'] = 0;
                }

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
                    if ($this->determineQualifiers($incentive)) {

                        //incentive amount for PAR
                        $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR']);
                        //incentive amount for Net Portfolio Growth
                        $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['net_portifolio_growth']);

                        //incentive amount for Net Client Growth
                        $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($incentive['net_client_growth']);

                        $incentives['sgl_records'] = 0;

                        //total incentive amount
                        $incentive['total_incentive_amount'] = ROUND(($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_amount_Net_Client_Growth']), 2);
                    } else {
                        $incentive['incentive_amount_PAR'] = 0;
                        $incentive['incentive_amount_Net_Portifolio_Growth'] = 0;
                        $incentive['incentive_amount_Net_Client_Growth'] = 0;
                        $incentive['total_incentive_amount'] = 0;
                        $incentives['sgl_records'] = 0;
                    }
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
        return response()->json(['incentives' => $incentivesWithDetails, 'message' => 'Incentives calculated successfully'], 200);

    }

    public function getAllIncentives()
    {
        $overallIndividualRecords = $this->overallIndividualRecords();
        $overallGroupRecords = $this->overallGroupRecords();
        $overallSGLRecords = $this->overallSGLRecords();

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
                ->distinct()->get(['customer_id'])
                ->count('customer_id');

            $netClientGrowth = $this->calculateNetClientGrowth($previousMonthUniqueCustomerCount, $record['unique_customer_id_individual']);
            $record['net_client_growth'] = $netClientGrowth;

            //add a flag that indicates the record is for individual
            $record['incentive_type'] = "individual";

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
                ->distinct()->get(['group_id'])
                ->count('group_id');

            $netClientGrowth = $this->calculateNetClientGrowth($previousMonthUniqueCustomerCount, $record['records_for_unique_group_id_group']);

            $record['net_client_growth'] = $netClientGrowth;
            //add a flag that indicates the record is for group
            $record['incentive_type'] = "group";
            $incentives[$staffId] = $record;
        }

        foreach ($overallSGLRecords as $staffId => $record) {
            $previousMonthOutstandingPrincipal = PreviousEndMonth::where('staff_id', $staffId)->sum('outsanding_principal');
            $record['previous_outstanding_principal_sgl'] = $previousMonthOutstandingPrincipal;
            $record['net_portifolio_growth'] = $this->calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $record['outstanding_principal_sgl']);
            $record['net_client_growth'] = 0;
            //add a flag that indicates the record is for sgl
            $record['incentive_type'] = "fast";
            $incentives[$staffId] = $record;
        }

        return $incentives;
    }

    /**
     * Individual client incentive parameters
     */
    public function calculateOutstandingPrincipalIndividual()
    {
        $outstandingPrincipalSumIndividual = Arrear::withoutGlobalScope(ArrearScope::class)->select('staff_id', DB::raw('SUM(outsanding_principal) as count'))
            ->where('lending_type', 'Individual')
            ->where('product_id', '!=', '21070')
            ->groupBy('staff_id')
            ->get();

        return $outstandingPrincipalSumIndividual;
    }

    /**
     * Individual client incentive parameters
     */
    public function calculateOutstandingPrincipalSGL()
    {
        $outstandingPrincipalSumSGL = Arrear::withoutGlobalScope(ArrearScope::class)->select('staff_id', DB::raw('SUM(outsanding_principal) as count'))
            ->where('product_id', '21070')
            ->groupBy('staff_id')
            ->get();

        return $outstandingPrincipalSumSGL;
    }

    //parameter 3
    public function calculateUniqueCustomerIDIndividual()
    {
        //group by staff_id by calculating the number of unique customer_id
        $uniqueCustomerIDIndividual = Arrear::withoutGlobalScope(ArrearScope::class)->select('staff_id', DB::raw('COUNT(DISTINCT customer_id) as count'))
            ->where('lending_type', 'Individual')
            ->where('product_id', '!=', '21070')
            ->groupBy('staff_id')
            ->get();

        return $uniqueCustomerIDIndividual;
    }
    //par per officer
    public function recordsForPARIndividual()
    {
        // Retrieve staff_id and PAR percentage directly from raw SQL query, rounded to 1 decimal place
        $recordsForPAR = Arrear::withoutGlobalScope(ArrearScope::class)
            ->where('lending_type', 'Individual')
            ->selectRaw('staff_id, ROUND(SUM(par) / SUM(outsanding_principal) * 100, 2) as count')
            ->whereRaw('(product_id != 21070)') // Exclude product ID 21070
            ->groupBy('staff_id')
            ->get();

        return $recordsForPAR;
    }

    //llr per officer
    public function recordsForMonthlyLoanLossRateIndividual()
    {
        // Calculate the monthly loan loss rate for each staff
        $monthlyLoanLossRate = Arrear::withoutGlobalScope(ArrearScope::class)
            ->where('lending_type', 'Individual')
            ->selectRaw('staff_id,
                round((SUM(CASE WHEN number_of_days_late > 180 THEN outsanding_principal ELSE 0 END) /
                 SUM(outsanding_principal)) * 100, 2) as count')
            ->where('product_id', '!=', '21070')
            ->groupBy('staff_id')
            ->get();

        return $monthlyLoanLossRate;
    }

    /**
     * Group client incentive parameters
     */

    //outstanding principal for group
    public function calculateOutstandingPrincipalGroup()
    {
        $outstandingPrincipalSumGroup = Arrear::withoutGlobalScope(ArrearScope::class)->select('staff_id', DB::raw('SUM(outsanding_principal) as count'))
            ->where('lending_type', 'Group')
            ->groupBy('staff_id')
            ->get();

        return $outstandingPrincipalSumGroup;
    }

    //number of total customers in a groups
    public function recordsForUniqueGroupIDGroup()
    {
        //group by staff_id by calculating the number of unique group_id
        $uniqueGroupIDGroup = Arrear::withoutGlobalScope(ArrearScope::class)->select('staff_id', DB::raw('COUNT(DISTINCT group_id) as count'))
            ->where('lending_type', 'Group')
            ->groupBy('staff_id')
            ->get();

        return $uniqueGroupIDGroup;
    }
    //par per officer
    public function recordsForPARGroup()
    {
        // Retrieve staff_id and PAR percentage directly from raw SQL query, rounded to 1 decimal place
        $recordsForPAR = Arrear::withoutGlobalScope(ArrearScope::class)
            ->where('lending_type', 'Group')
            ->selectRaw('staff_id, ROUND(SUM(par) / SUM(outsanding_principal) * 100, 2) as count')
            ->whereRaw('(product_id != 21070)') // Exclude product ID 21070
            ->groupBy('staff_id')
            ->get();

        return $recordsForPAR;
    }
    //llr per officer
    public function recordsForMonthlyLoanLossRatellrGroup()
    {
        // Calculate the monthly loan loss rate for each staff
        $monthlyLoanLossRate = Arrear::withoutGlobalScope(ArrearScope::class)
            ->where('lending_type', 'Group')
            ->selectRaw('staff_id,
            round((SUM(CASE WHEN number_of_days_late > 180 THEN outsanding_principal ELSE 0 END) /
             SUM(outsanding_principal)) * 100, 2) as count')
            ->where('product_id', '!=', '21070')
            ->groupBy('staff_id')
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
        $recordsForPAR = $this->recordsForPARIndividual();
        $monthlyLoanLossRate = $this->recordsForMonthlyLoanLossRateIndividual();

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
        $recordsForPAR = $this->recordsForPARGroup();
        $monthlyLoanLossRate = $this->recordsForMonthlyLoanLossRatellrGroup();

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
        return $overallGroupRecords;
    }

    /**
     * SGL Incentive parameters
     */

    //number of groups PAR
    public function recordsForNoOfGroupsPAR()
    {
        // Retrieve staff_id and PAR percentage directly from raw SQL query, rounded to 1 decimal place
        $recordsForPAR = Arrear::withoutGlobalScope(ArrearScope::class)->selectRaw('staff_id, ROUND(SUM(par) / SUM(outsanding_principal) * 100, 2) as count')
            ->whereRaw('(product_id = 21070)')
            ->groupBy('staff_id')
            ->get();

        return $recordsForPAR;
    }

    //number of groups LLR
    public function recordsForMonthlyLoanLossRateGroup()
    {
        // Calculate the monthly loan loss rate for each staff
        $monthlyLoanLossRate = Arrear::withoutGlobalScope(ArrearScope::class)->selectRaw('staff_id,
            round((SUM(CASE WHEN number_of_days_late > 180 THEN outsanding_principal ELSE 0 END) /
             SUM(outsanding_principal)) * 100, 2) as count')
            ->where('product_id', '21070')
            ->groupBy('staff_id')
            ->get();

        return $monthlyLoanLossRate;
    }

    //number of groups customer
    public function recordsForNoOfGroupCustomer()
    {
        $noOfGroups = Arrear::withoutGlobalScope(ArrearScope::class)->select('staff_id', DB::raw('COUNT(customer_id) as count'))
            ->where('product_id', '21070')
            ->groupBy('staff_id')
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
        $outstandingPrincipalSGL = $this->calculateOutstandingPrincipalSGL();

        $overallSGLRecords = [];

        foreach ($outstandingPrincipalSGL as $record) {
            $staffId = $record->staff_id;
            $staffId = $record->staff_id;
            if (!isset($overallSGLRecords[$staffId])) {
                $overallSGLRecords[$staffId] = [];
            }
            $overallSGLRecords[$staffId]['outstanding_principal_sgl'] = $record->count;
        }

        foreach ($recordsForNoOfGroupsPAR as $record) {
            $staffId = $record->staff_id;
            $staffId = $record->staff_id;
            if (!isset($overallSGLRecords[$staffId])) {
                $overallSGLRecords[$staffId] = [];
            }
            $overallSGLRecords[$staffId][
                'records_for_PAR'] = $record->count;
        }

        foreach ($recordsForMonthlyLoanLossRateGroup as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallSGLRecords[$staffId])) {
                $overallSGLRecords[$staffId] = [];
            }
            $overallSGLRecords[$staffId]['monthly_loan_loss_rate'] = $record->count;
        }

        foreach ($recordsForNoOfGroupCustomer as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallSGLRecords[$staffId])) {
                $overallSGLRecords[$staffId] = [];
            }
            $overallSGLRecords[$staffId]['sgl_records'] = $record->count;
        }

        // //filter only those with sgl_records property or has all [recordsForNoOfGroupsPAR, recordsForMonthlyLoanLossRateGroup, recordsForNoOfGroupCustomer]
        // $overallSGLRecords = array_filter($overallSGLRecords, function ($record) {
        //     return isset($record['recordsForNoOfGroupsPAR']) && isset($record['recordsForMonthlyLoanLossRateGroup']) && isset($record['recordsForNoOfGroupCustomer']);
        // });

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
        $maxPar = IncentiveSettings::first()->max_par;
        $parPercentage = IncentiveSettings::first()->percentage_incentive_par;
        $maximumIncentive = IncentiveSettings::first()->max_incentive;
        $amount = 0;
        if (($par / 100) <= ($maxPar / 100)) {
            $amount = ((($maxPar / 100) - ($par / 100)) / ($maxPar / 100)) * ($parPercentage / 100) * $maximumIncentive;
        }

        return ROUND($amount, 2);
    }
    //calculate incentive for SGL(par)
    public function calculateIncentiveAmountPARSGL($par)
    {
        $maxPar = IncentiveSettings::first()->max_par_fast;
        $parPercentage = 20;
        $maximumIncentive = IncentiveSettings::first()->max_incentive;
        $amount = 0;
        if (($par / 100) <= ($maxPar / 100)) {
            $amount = ((($maxPar / 100) - ($par / 100)) / ($maxPar / 100)) * ($parPercentage / 100) * $maximumIncentive;
        }

        return ROUND($amount, 2);
    }

    public function calculateIncentiveAmountNetPortifolioGrowth($outstandingPrincipalIndividual)
    {
        $max = IncentiveSettings::first()->max_cap_portifolio;
        $min = IncentiveSettings::first()->min_cap_portifolio;
        $portifolioPercentage = IncentiveSettings::first()->percentage_incentive_portifolio;
        $maximumIncentive = IncentiveSettings::first()->max_incentive;
        $actual = $outstandingPrincipalIndividual;
        $amount = 0;

        //if $actual is less than  50000000
        if (($actual > $min) && ($actual < $max)) {
            $amount = (ROUND(($actual - $min) / ($max - $min), 2)) * ($portifolioPercentage / 100) * $maximumIncentive;
        }
        //greater than 40000000
        if ($actual >= $max) {
            $amount = ($portifolioPercentage / 100) * $maximumIncentive;
        }

        return ROUND($amount, 2);
    }

    //net portifolio growth for SGL incentive
    public function calculateIncentiveAmountNetPortifolioGrowthSGL($outstandingPrincipalSGL)
    {
        $max = IncentiveSettings::first()->max_cap_portifolio_fast;
        $min = IncentiveSettings::first()->min_cap_portifolio_fast;
        $portifolioPercentage = 40;
        $maximumIncentive = IncentiveSettings::first()->max_incentive;
        $actual = $outstandingPrincipalSGL;
        $amount = 0;

        //if $actual is less than  50000000
        if (($actual > $min) && ($actual < $max)) {
            $amount = (ROUND(($actual - $min) / ($max - $min), 2)) * ($portifolioPercentage / 100) * $maximumIncentive;
        }
        //greater than 40000000
        if ($actual >= $max) {
            $amount = ($portifolioPercentage / 100) * $maximumIncentive;
        }

        return ROUND($amount, 2);
    }

    public function calculateIncentiveAmountNetClientGrowth($uniqueCustomerIDIndividual)
    {
        $max = IncentiveSettings::first()->max_cap_client;
        $min = IncentiveSettings::first()->min_cap_client;
        $clientPercentage = IncentiveSettings::first()->percentage_incentive_client;
        $maximumIncentive = IncentiveSettings::first()->max_incentive;

        $actual = $uniqueCustomerIDIndividual;
        $amount = 0;

        if ($actual >= 5) {
            $amount = (($actual - $min) / ($max - $min)) * ($clientPercentage / 100) * $maximumIncentive;
        }

        //if $actual is greater than 20
        if ($actual >= $max) {
            $amount = ($clientPercentage / 100) * $maximumIncentive;
        }

        return ROUND($amount, 2);
    }
    //calculate incentive for SGL(no of groups)
    public function calculateIncentiveAmountSGLGroups($numberOfGroupsSGL)
    {
        $max = IncentiveSettings::first()->max_cap_number_of_groups_fast;
        $min = IncentiveSettings::first()->min_cap_number_of_groups_fast;
        $clientPercentage = 40;
        $maximumIncentive = IncentiveSettings::first()->max_incentive;

        $actual = $numberOfGroupsSGL;
        $amount = 0;

        if ($actual >= $min) {
            $amount = (($actual - $min) / ($max - $min)) * ($clientPercentage / 100) * $maximumIncentive;
        }

        //if $actual is greater than 20
        if ($actual >= $max) {
            $amount = ($clientPercentage / 100) * $maximumIncentive;
        }

        return ROUND($amount, 2);
    }

    //function to determine qualifiers
    public function determineQualifiers($incentive)
    {
        $min_cap_portifolio_individual = IncentiveSettings::first()->min_cap_portifolio_individual;
        $min_cap_portifolio_group = IncentiveSettings::first()->min_cap_portifolio_group;
        $min_cap_portifolio_sgl = IncentiveSettings::first()->min_cap_portifolio_fast;
        $min_cap_client_individual = IncentiveSettings::first()->min_cap_client_individual;
        $min_cap_client_group = IncentiveSettings::first()->min_cap_client_group;
        $max_par_individual = IncentiveSettings::first()->max_par_individual;
        $max_par_group = IncentiveSettings::first()->max_par_group;
        $max_par_fast = IncentiveSettings::first()->max_par_fast;
        $max_llr_group = IncentiveSettings::first()->max_llr_group;
        $max_llr_individual = IncentiveSettings::first()->max_llr_individual;
        $max_llr_fast = IncentiveSettings::first()->max_llr_fast;
        $min_cap_number_of_groups_sgl = IncentiveSettings::first()->min_cap_number_of_groups_fast;
        //check if incentive is individual by checking for the presence of outstanding_principal_individual
        if (array_key_exists('outstanding_principal_individual', $incentive)) {
            $outstanding_principal_individual = $incentive['outstanding_principal_individual'];
            $unique_customer_id_individual = $incentive['unique_customer_id_individual'];
            $records_for_PAR = $incentive['records_for_PAR'];
            $monthly_loan_loss_rate = $incentive['monthly_loan_loss_rate'];

            //check if the staff qualifies for the incentive
            if ($outstanding_principal_individual >= $min_cap_portifolio_individual && $unique_customer_id_individual >= $min_cap_client_individual && $records_for_PAR <= $max_par_individual && $monthly_loan_loss_rate <= $max_llr_individual) {
                return true;
            }
        }

        //check if incentive is group by checking for the presence of outstanding_principal_group
        if (array_key_exists('outstanding_principal_group', $incentive)) {
            $outstanding_principal_group = $incentive['outstanding_principal_group'];
            $records_for_unique_group_id_group = $incentive['records_for_unique_group_id_group'];
            $records_for_PAR = $incentive['records_for_PAR'];
            $monthly_loan_loss_rate = $incentive['monthly_loan_loss_rate'];

            //check if the staff qualifies for the incentive
            if ($outstanding_principal_group >= $min_cap_portifolio_group && $records_for_unique_group_id_group >= $min_cap_client_group && $records_for_PAR <= $max_par_group && $monthly_loan_loss_rate <= $max_llr_group) {
                return true;
            }
        }

        if (array_key_exists('outstanding_principal_sgl', $incentive)) {
            $records_for_PAR = $incentive['records_for_PAR'];
            $monthly_loan_loss_rate = $incentive['monthly_loan_loss_rate'];
            $number_of_groups = $incentive['sgl_records'];
            $outstanding_principal_sgl = $incentive['outstanding_principal_sgl'];

            //check if the staff qualifies for the incentive
            if ($number_of_groups >= $min_cap_number_of_groups_sgl && $records_for_PAR <= $max_par_fast && $monthly_loan_loss_rate <= $max_llr_fast && $outstanding_principal_sgl >= $min_cap_portifolio_sgl) {
                return true;
            }
        }

        return false;
    }

}
