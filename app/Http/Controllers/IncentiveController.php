<?php

namespace App\Http\Controllers;

use App\Models\Arrear;
use App\Models\IncentiveSettings;
use App\Models\Officer;
use App\Models\PreviousEndMonth;
use App\Models\Scopes\ArrearScope;
use Illuminate\Http\Request;
//import ArrerScope
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


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
        /** @var \App\Models\Officer $officer */
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
                            Log::debug("PRE-QUALIFICATION METRICS for staff {$staffId}", [
                                'type' => $incentive['incentive_type'] ?? 'unknown',
                                'loan_portfolio' => $incentive['outstanding_principal_mse'] ?? $incentive['outstanding_principal_individual'] ?? $incentive['outstanding_principal_group'] ?? 'N/A',
                                'active_clients' => $incentive['unique_customer_id_mse'] ?? $incentive['unique_customer_id_individual'] ?? $incentive['records_for_unique_group_id_group'] ?? 'N/A',
                                'PAR' => $incentive['records_for_PAR'] ?? 'N/A',
                                'LLR' => $incentive['monthly_loan_loss_rate'] ?? 'N/A',
                                'retention' => $this->calculateClientRetention($staffId, $incentive['incentive_type'] ?? 'unknown'),
                                'net_portfolio_growth' => $incentive['net_portifolio_growth'] ?? 'N/A',
                                'net_client_growth' => $incentive['net_client_growth'] ?? 'N/A',
                            ]);
                if ($this->determineQualifiers($incentive)) {
                    if (array_key_exists('outstanding_principal_fast', $incentive)) {
                        //incentive amount for PAR
                        $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPARFAST($incentive['records_for_PAR']);
                        //incentive amount for Net Portfolio Growth
                        $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowthFAST($incentive['net_portifolio_growth']);
                        //incentive amount for Net Client Growth
                        $incentive['incentive_number_of_fast_groups'] = $this->calculateIncentiveAmountFASTGroups($incentive['fast_records']);
                        //retention score
                        $incentive['incentive_retention_score'] = $this->calculateRetentionScore($staffId, $incentive['incentive_type']);
                        //total incentive amount
                        $incentive['total_incentive_amount'] = ROUND(($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_number_of_fast_groups'] + $incentive['incentive_retention_score']), 2);
                    } elseif (array_key_exists('outstanding_principal_mse', $incentive)) {
                        //MSE logic (new)
                        $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPARMSE($incentive['records_for_PAR']);
                        $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowthMSE($incentive['net_portifolio_growth']);
                        $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowthMSE($incentive['net_client_growth']);
                        $incentive['incentive_retention_score'] = $this->calculateRetentionScore($staffId, $incentive['incentive_type']);
                        $incentive['total_incentive_amount'] = round(
                            $incentive['incentive_amount_PAR'] +
                                $incentive['incentive_amount_Net_Portifolio_Growth'] +
                                $incentive['incentive_amount_Net_Client_Growth'] +
                                $incentive['incentive_retention_score'],
                            2
                        );
                    } else {
                        //incentive amount for PAR
                        $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR']);
                        //incentive amount for Net Portfolio Growth
                        $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['net_portifolio_growth']);

                        //incentive amount for Net Client Growth
                        $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($incentive['net_client_growth']);

                        $incentives['fast_records'] = 0;

                        $incentive['incentive_retention_score'] = $this->calculateRetentionScore($staffId, $incentive['incentive_type']);

                        //total incentive amount
                        $incentive['total_incentive_amount'] = ROUND(($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_amount_Net_Client_Growth'] + $incentive['incentive_retention_score']), 2);
                    }
                } else {
                    $incentive['incentive_amount_PAR'] = 0;
                    $incentive['incentive_amount_Net_Portifolio_Growth'] = 0;
                    $incentive['incentive_amount_Net_Client_Growth'] = 0;
                    $incentive['incentive_retention_score'] = 0;
                    $incentive['total_incentive_amount'] = 0;
                    $incentives['fast_records'] = 0;
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

                        $incentives['fast_records'] = 0;

                        $incentive['incentive_retention_score'] = $this->calculateRetentionScore($staffId, $incentive['incentive_type']);

                        //total incentive amount
                        $incentive['total_incentive_amount'] = ROUND(($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_amount_Net_Client_Growth'] + $incentive['incentive_retention_score']), 2);
                    } else {
                        $incentive['incentive_amount_PAR'] = 0;
                        $incentive['incentive_amount_Net_Portifolio_Growth'] = 0;
                        $incentive['incentive_amount_Net_Client_Growth'] = 0;
                        $incentive['incentive_retention_score'] = 0;
                        $incentive['total_incentive_amount'] = 0;
                        $incentives['fast_records'] = 0;
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
        $overallFASTRecords = $this->overallFASTRecords();
        $overallMSERecords = $this->overallMSERecords();

       

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
            $record['fast_records'] = 0;
            $netPortifolioGrowth = $this->calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $record['outstanding_principal_individual']);
            $record['net_portifolio_growth'] = $netPortifolioGrowth;

            $previousMonthUniqueCustomerCount = PreviousEndMonth::where('staff_id', $staffId)
                ->where('lending_type', 'Individual')
                ->distinct()->get(['customer_id'])
                ->count();

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
                ->count();

            $netClientGrowth = $this->calculateNetClientGrowth($previousMonthUniqueCustomerCount, $record['records_for_unique_group_id_group']);

            $record['net_client_growth'] = $netClientGrowth;
            //add a flag that indicates the record is for group
            $record['incentive_type'] = "group";
            $incentives[$staffId] = $record;
        }

        foreach ($overallFASTRecords as $staffId => $record) {
            $previousMonthOutstandingPrincipal = PreviousEndMonth::where('staff_id', $staffId)->sum('outsanding_principal');
            $record['previous_outstanding_principal_fast'] = $previousMonthOutstandingPrincipal;
            $record['net_portifolio_growth'] = $this->calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $record['outstanding_principal_fast']);
            $record['net_client_growth'] = 0;
            //add a flag that indicates the record is for fast
            $record['incentive_type'] = "fast";
            $incentives[$staffId] = $record;
        }

      

        foreach ($overallMSERecords as $staffId => $record) {
            $previousMonthOutstandingPrincipal = PreviousEndMonth::where('staff_id', $staffId)->sum('outsanding_principal');
            $record['previous_outstanding_principal_mse'] = $previousMonthOutstandingPrincipal;

            $record['outstanding_principal_individual'] = 0;
            $record['outstanding_principal_group'] = 0;
            $record['outstanding_principal_fast'] = 0;
            $record['records_for_unique_group_id_group'] = 0;
            $record['fast_records'] = 0;

            $netPortifolioGrowth = $this->calculateNetPortifolioGrowth(
                $previousMonthOutstandingPrincipal,
                $record['outstanding_principal_mse']
            );
            $record['net_portifolio_growth'] = $netPortifolioGrowth;

            $previousMonthUniqueCustomerCount = PreviousEndMonth::where('staff_id', $staffId)
                ->where('lending_type', 'mse')
                ->distinct()
                ->count('customer_id');

            $netClientGrowth = $this->calculateNetClientGrowth(
                $previousMonthUniqueCustomerCount,
                $record['unique_customer_id_mse']
            );
            $record['net_client_growth'] = $netClientGrowth;

            $record['incentive_type'] = 'mse';

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
    public function calculateOutstandingPrincipalFAST()
    {
        $outstandingPrincipalSumFAST = Arrear::withoutGlobalScope(ArrearScope::class)->select('staff_id', DB::raw('SUM(outsanding_principal) as count'))
            ->where('product_id', '21070')
            ->groupBy('staff_id')
            ->get();

        return $outstandingPrincipalSumFAST;
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

    // PAR FOR MSE OFFICER
    private function recordsForPARMSE()
    {
        // Retrieve staff_id and PAR percentage directly from raw SQL query, rounded to 1 decimal place
        $recordsForPAR = Arrear::withoutGlobalScope(ArrearScope::class)
            ->where('lending_type', 'mse')
            ->selectRaw('staff_id, ROUND(SUM(par) / SUM(outsanding_principal) * 100, 2) as count')
            ->whereRaw('(product_id != 21070)') // Exclude product ID 21070
            ->groupBy('staff_id')
            ->get();

        return $recordsForPAR;
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

    //llr per MSE OFFICER
    public function recordsForMonthlyLoanLossRateMSE()
    {
        // Calculate the monthly loan loss rate for each staff
        $monthlyLoanLossRate = Arrear::withoutGlobalScope(ArrearScope::class)
            ->where('lending_type', 'mse')
            ->selectRaw('staff_id,
                round((SUM(CASE WHEN number_of_days_late > 180 THEN outsanding_principal ELSE 0 END) /
                 SUM(outsanding_principal)) * 100, 2) as count')
            ->where('product_id', '!=', '21070')
            ->groupBy('staff_id')
            ->get();

        return $monthlyLoanLossRate;
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

    public function overallMSERecords()
    {
        $outstandingPrincipalMSE = Arrear::withoutGlobalScope(ArrearScope::class)
            ->where('lending_type', 'mse')
            ->select('staff_id', DB::raw('SUM(outsanding_principal) as count'))
            ->groupBy('staff_id')
            ->get();

        $uniqueCustomerIDMSE = Arrear::withoutGlobalScope(ArrearScope::class)
            ->where('lending_type', 'mse')
            ->select('staff_id', DB::raw('COUNT(DISTINCT customer_id) as count'))
            ->groupBy('staff_id')
            ->get();

        $recordsForPAR = $this->recordsForPARMSE(); // already defined
        $monthlyLoanLossRate = $this->recordsForMonthlyLoanLossRateMSE();

        $overallMSERecords = [];

        // Log::info('overallFASTRecords Records:', $outstandingPrincipalMSE);
        // Log::info('overallFASTRecords Records:', $uniqueCustomerIDMSE);
        // Log::info('overallFASTRecords Records:', $recordsForPAR);
        // Log::info('overallFASTRecords Records:', $monthlyLoanLossRate);


        Log::debug('MSE Outstanding Principals', $outstandingPrincipalMSE->toArray());
        Log::debug('MSE Unique Customers', $uniqueCustomerIDMSE->toArray());
        Log::debug('MSE recordsForPAR', $recordsForPAR->toArray());
        Log::debug('MSE monthlyLoanLossRate', $monthlyLoanLossRate->toArray());



        foreach ($outstandingPrincipalMSE as $record) {
            $staffId = $record->staff_id;
            $overallMSERecords[$staffId] = [
                'outstanding_principal_mse' => $record->count,
            ];
        }

        foreach ($uniqueCustomerIDMSE as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallMSERecords[$staffId])) {
                $overallMSERecords[$staffId] = [];
            }
            $overallMSERecords[$staffId]['unique_customer_id_mse'] = $record->count;
        }

        foreach ($recordsForPAR as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallMSERecords[$staffId])) {
                $overallMSERecords[$staffId] = [];
            }
            $overallMSERecords[$staffId]['records_for_PAR'] = $record->count;
        }

        foreach ($monthlyLoanLossRate as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallMSERecords[$staffId])) {
                $overallMSERecords[$staffId] = [];
            }
            $overallMSERecords[$staffId]['monthly_loan_loss_rate'] = $record->count;
        }

        // Final filtering: make sure all required keys are present
        $overallMSERecords = array_filter($overallMSERecords, function ($record) {
            return isset($record['outstanding_principal_mse']) &&
                isset($record['unique_customer_id_mse']) &&
                isset($record['records_for_PAR']) &&
                isset($record['monthly_loan_loss_rate']);
        });


        return $overallMSERecords;
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

        //filter only those with fast_records property or has all [outstanding_principal_individual, unique_customer_id_individual, records_for_PAR, monthly_loan_loss_rate]
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
     * FAST Incentive parameters
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
     * overall FAST records meeting criteria in the order
     * recordsForNoOfGroupsPAR
     * recordsForMonthlyLoanLossRateGroup
     * recordsForNoOfGroupCustomer
     * by merging the results, we can get the staff_id that meets all the criteria
     */
    public function overallFASTRecords()
    {
        $recordsForNoOfGroupsPAR = $this->recordsForNoOfGroupsPAR();
        $recordsForMonthlyLoanLossRateGroup = $this->recordsForMonthlyLoanLossRateGroup();
        $recordsForNoOfGroupCustomer = $this->recordsForNoOfGroupCustomer();
        $outstandingPrincipalFAST = $this->calculateOutstandingPrincipalFAST();

        $overallFASTRecords = [];

        foreach ($outstandingPrincipalFAST as $record) {
            $staffId = $record->staff_id;
            $staffId = $record->staff_id;
            if (!isset($overallFASTRecords[$staffId])) {
                $overallFASTRecords[$staffId] = [];
            }
            $overallFASTRecords[$staffId]['outstanding_principal_fast'] = $record->count;
        }

        foreach ($recordsForNoOfGroupsPAR as $record) {
            $staffId = $record->staff_id;
            $staffId = $record->staff_id;
            if (!isset($overallFASTRecords[$staffId])) {
                $overallFASTRecords[$staffId] = [];
            }
            $overallFASTRecords[$staffId][
                'records_for_PAR'] = $record->count;
        }

        foreach ($recordsForMonthlyLoanLossRateGroup as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallFASTRecords[$staffId])) {
                $overallFASTRecords[$staffId] = [];
            }
            $overallFASTRecords[$staffId]['monthly_loan_loss_rate'] = $record->count;
        }

        foreach ($recordsForNoOfGroupCustomer as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallFASTRecords[$staffId])) {
                $overallFASTRecords[$staffId] = [];
            }
            $overallFASTRecords[$staffId]['fast_records'] = $record->count;
        }

        // //filter only those with fast_records property or has all [recordsForNoOfGroupsPAR, recordsForMonthlyLoanLossRateGroup, recordsForNoOfGroupCustomer]
        // $overallFASTRecords = array_filter($overallFASTRecords, function ($record) {
        //     return isset($record['recordsForNoOfGroupsPAR']) && isset($record['recordsForMonthlyLoanLossRateGroup']) && isset($record['recordsForNoOfGroupCustomer']);
        // });

        return $overallFASTRecords;
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
        $maxPar = IncentiveSettings::first()->max_par_individual;
        $parPercentage = IncentiveSettings::first()->percentage_incentive_par_individual;
        $maximumIncentive = IncentiveSettings::first()->max_incentive;
        $amount = 0;
        if (($par / 100) <= ($maxPar / 100)) {
            $amount = ((($maxPar / 100) - ($par / 100)) / ($maxPar / 100)) * ($parPercentage / 100) * $maximumIncentive;
        }

        return ROUND($amount, 2);
    }


    // PAR INCENTIVE FOR GROUP
    public function calculateIncentiveAmountPARGroup($par)
    {
        $maxPar = IncentiveSettings::first()->max_par_group;
        $parPercentage = IncentiveSettings::first()->percentage_incentive_par_group;
        $maximumIncentive = IncentiveSettings::first()->max_incentive;
        $amount = 0;
        if (($par / 100) <= ($maxPar / 100)) {
            $amount = ((($maxPar / 100) - ($par / 100)) / ($maxPar / 100)) * ($parPercentage / 100) * $maximumIncentive;
        }

        return ROUND($amount, 2);
    }

    //calculate incentive for FAST(par)
    public function calculateIncentiveAmountPARFAST($par)
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

    public function calculateIncentiveAmountPARMSE($par)
    {
        $maxPar = IncentiveSettings::first()->max_par_mse;
        $parPercentage = IncentiveSettings::first()->percentage_incentive_par_mse;
        $maximumIncentive = IncentiveSettings::first()->max_incentive_mse;
        $amount = 0;
        if (($par / 100) <= ($maxPar / 100)) {
            $amount = ((($maxPar / 100) - ($par / 100)) / ($maxPar / 100)) * ($parPercentage / 100) * $maximumIncentive;
        }

        return ROUND($amount, 2);
    }

    public function calculateIncentiveAmountNetPortifolioGrowth($outstandingPrincipalIndividual)
    {
        $max = IncentiveSettings::first()->max_net_portfolio_growth_indv;
        $min = IncentiveSettings::first()->min_net_portfolio_growth_indv;
        $portifolioPercentage = IncentiveSettings::first()->net_portfolio_growth_weight_indv;
        $maximumIncentive = IncentiveSettings::first()->max_incentive;
        $actual = $outstandingPrincipalIndividual;
        $amount = 0;

        if ($actual <= $min) {
        return 0;
    }

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

    
    public function calculateIncentiveAmountNetPortifolioGrowthMSE($outstandingPrincipalIndividualMSE)
    {
        $max = IncentiveSettings::first()->max_net_portfolio_growth_mse;
        $min = IncentiveSettings::first()->min_net_portfolio_growth_mse;
        $portifolioPercentage = IncentiveSettings::first()->net_portfolio_growth_weight_mse;
        $maximumIncentive = IncentiveSettings::first()->max_incentive_mse;
        $actual = $outstandingPrincipalIndividualMSE;
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

    //net portifolio growth for FAST incentive
    public function calculateIncentiveAmountNetPortifolioGrowthFAST($outstandingPrincipalFAST)
    {
        $max = IncentiveSettings::first()->max_net_portfolio_growth_fast;
        $min = IncentiveSettings::first()->min_net_portfolio_growth_fast;
        $portifolioPercentage = 40;
        $maximumIncentive = IncentiveSettings::first()->max_incentive_fast;
        $actual = $outstandingPrincipalFAST;
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


    public function calculateIncentiveAmountNetClientGrowthMSE($actual)
    {
        $max = IncentiveSettings::first()->max_cap_client_growth_mse; // 20
        $min = IncentiveSettings::first()->min_cap_client_growth_mse; // 5
        $weight = IncentiveSettings::first()->percentage_incentive_client_growth_mse; //  20%
        $maximumIncentive = IncentiveSettings::first()->max_incentive_mse;

        $amount = 0;


        if ($actual <= $min) {
            return 0; // Below or equal to min gets no incentive
        }

        if ($actual >= $max) {
            $amount = ($weight / 100) * $maximumIncentive;
        } else {
            $amount = (($actual - $min) / ($max - $min)) * ($weight / 100) * $maximumIncentive;
        }

        return round($amount, 2);
    }

    public function calculateIncentiveAmountNetClientGrowth($uniqueCustomerIDIndividual)
    {
        $max = IncentiveSettings::first()->max_cap_client;
        $min = IncentiveSettings::first()->min_cap_client;
        $clientPercentage = IncentiveSettings::first()->percentage_incentive_client;
        $maximumIncentive = IncentiveSettings::first()->max_incentive;

        $actual = $uniqueCustomerIDIndividual;
        $amount = 0;

        $amount = 0;

        if ($actual <= $min) {
            return 0; 
        }

        if ($actual >= $max) {
            $amount = ($clientPercentage / 100) * $maximumIncentive;
        } else {
            $amount = (($actual - $min) / ($max - $min)) * ($clientPercentage / 100) * $maximumIncentive;
        }

        return round($amount, 2);
    }
    //calculate incentive for FAST(no of groups)
    public function calculateIncentiveAmountFASTGroups($numberOfGroupsFAST)
    {
        $max = IncentiveSettings::first()->max_cap_number_of_groups_fast;
        $min = IncentiveSettings::first()->min_cap_number_of_groups_fast;
        $clientPercentage = 40;
        $maximumIncentive = IncentiveSettings::first()->max_incentive;

        $actual = $numberOfGroupsFAST;
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

    public function settings()
    {
        $incentiveSettings = IncentiveSettings::first();
        //convert all the attributes to double
        $incentiveSettings->max_cap_portifolio = doubleval($incentiveSettings->max_cap_portifolio);
        $incentiveSettings->min_cap_portifolio = doubleval($incentiveSettings->min_cap_portifolio);
        $incentiveSettings->max_cap_client = doubleval($incentiveSettings->max_cap_client);
        $incentiveSettings->min_cap_client = doubleval($incentiveSettings->min_cap_client);
        $incentiveSettings->max_incentive = doubleval($incentiveSettings->max_incentive);
        $incentiveSettings->max_cap_portifolio_individual = doubleval($incentiveSettings->max_cap_portifolio_individual);
        $incentiveSettings->max_cap_portifolio_group = doubleval($incentiveSettings->max_cap_portifolio_group);
        $incentiveSettings->min_cap_client_individual = doubleval($incentiveSettings->min_cap_client_individual);
        $incentiveSettings->min_cap_client_group = doubleval($incentiveSettings->min_cap_client_group);
        $incentiveSettings->max_par_individual = doubleval($incentiveSettings->max_par_individual);
        $incentiveSettings->max_par_group = doubleval($incentiveSettings->max_par_group);
        $incentiveSettings->max_par_fast = doubleval($incentiveSettings->max_par_fast);
        $incentiveSettings->max_llr_group = doubleval($incentiveSettings->max_llr_group);
        $incentiveSettings->max_llr_individual = doubleval($incentiveSettings->max_llr_individual);
        $incentiveSettings->max_llr_fast = doubleval($incentiveSettings->max_llr_fast);
        $incentiveSettings->min_cap_number_of_groups_fast = doubleval($incentiveSettings->min_cap_number_of_groups_fast);
        // min cap portifolio for fast
        $incentiveSettings->min_cap_portifolio_fast = doubleval($incentiveSettings->min_cap_portifolio_fast);


        $incentiveSettings->retention_min_individual = doubleval($incentiveSettings->retention_min_individual);
        $incentiveSettings->min_cap_portifolio_fast = doubleval($incentiveSettings->min_cap_portifolio_fast);

        $incentiveSettings->min_net_portfolio_growth_indv = doubleval($incentiveSettings->min_net_portfolio_growth_indv);
        $incentiveSettings->max_net_portfolio_growth_indv = doubleval($incentiveSettings->max_net_portfolio_growth_indv);
        $incentiveSettings->net_portfolio_growth_weight_indv = doubleval($incentiveSettings->net_portfolio_growth_weight_indv);
        

        return view('incentive-settings', compact('incentiveSettings'));
    }

    public function update_incentive_settings(Request $request)
    {
        $requestData = $request->all();
        $incentiveSettings = IncentiveSettings::first();

        // Remove commas from numeric fields
        $numericFields = [
            'max_cap_portifolio',
            'min_cap_portifolio',
            'max_cap_client',
            'min_cap_client',
            'max_incentive',
            'max_cap_portifolio_individual',
            'max_cap_portifolio_group',
            'min_cap_client_individual',
            'min_cap_client_group',
            'max_par_individual',
        ];
        foreach ($numericFields as $field) {
            if (isset($requestData[$field])) {
                $requestData[$field] = str_replace(',', '', $requestData[$field]);
            }
        }

        if ($incentiveSettings) {
            $incentiveSettings->update($requestData);
        } else {
            IncentiveSettings::create($requestData);
        }
        // Add a session flash message to indicate success
        session()->flash('success', 'Incentive settings updated successfully!');

        // Redirect back to the settings page
        return redirect()->route('incentives-settings');
    }

    //function to determine qualifiers
    // public function determineQualifiers($incentive)
    // {
    //     $min_cap_portifolio_individual = IncentiveSettings::first()->min_cap_portifolio_individual;
    //     $min_cap_portifolio_group = IncentiveSettings::first()->min_cap_portifolio_group;
    //     $min_cap_portifolio_fast = IncentiveSettings::first()->min_cap_portifolio_fast;
    //     $min_cap_client_individual = IncentiveSettings::first()->min_cap_client_individual;
    //     $min_cap_client_group = IncentiveSettings::first()->min_cap_client_group;
    //     $max_par_individual = IncentiveSettings::first()->max_par_individual;
    //     $max_par_group = IncentiveSettings::first()->max_par_group;
    //     $max_par_fast = IncentiveSettings::first()->max_par_fast;
    //     $max_llr_group = IncentiveSettings::first()->max_llr_group;
    //     $max_llr_individual = IncentiveSettings::first()->max_llr_individual;
    //     $max_llr_fast = IncentiveSettings::first()->max_llr_fast;
    //     $min_cap_number_of_groups_fast = IncentiveSettings::first()->min_cap_number_of_groups_fast;
    //     //check if incentive is individual by checking for the presence of outstanding_principal_individual
    //     if (array_key_exists('outstanding_principal_individual', $incentive)) {
    //         $outstanding_principal_individual = $incentive['outstanding_principal_individual'];
    //         $unique_customer_id_individual = $incentive['unique_customer_id_individual'];
    //         $records_for_PAR = $incentive['records_for_PAR'];
    //         $monthly_loan_loss_rate = $incentive['monthly_loan_loss_rate'];

    //         //check if the staff qualifies for the incentive
    //         if ($outstanding_principal_individual >= $min_cap_portifolio_individual && $unique_customer_id_individual >= $min_cap_client_individual && $records_for_PAR <= $max_par_individual && $monthly_loan_loss_rate <= $max_llr_individual) {
    //             return true;
    //         }
    //     }

    //     //check if incentive is group by checking for the presence of outstanding_principal_group
    //     if (array_key_exists('outstanding_principal_group', $incentive)) {
    //         $outstanding_principal_group = $incentive['outstanding_principal_group'];
    //         $records_for_unique_group_id_group = $incentive['records_for_unique_group_id_group'];
    //         $records_for_PAR = $incentive['records_for_PAR'];
    //         $monthly_loan_loss_rate = $incentive['monthly_loan_loss_rate'];

    //         //check if the staff qualifies for the incentive
    //         if ($outstanding_principal_group >= $min_cap_portifolio_group && $records_for_unique_group_id_group >= $min_cap_client_group && $records_for_PAR <= $max_par_group && $monthly_loan_loss_rate <= $max_llr_group) {
    //             return true;
    //         }
    //     }

    //     if (array_key_exists('outstanding_principal_fast', $incentive)) {
    //         $records_for_PAR = $incentive['records_for_PAR'];
    //         $monthly_loan_loss_rate = $incentive['monthly_loan_loss_rate'];
    //         $number_of_groups = $incentive['fast_records'];
    //         $outstanding_principal_fast = $incentive['outstanding_principal_fast'];

    //         //check if the staff qualifies for the incentive
    //         if ($number_of_groups >= $min_cap_number_of_groups_fast && $records_for_PAR <= $max_par_fast && $monthly_loan_loss_rate <= $max_llr_fast && $outstanding_principal_fast >= $min_cap_portifolio_fast) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }


    // TODO: COME BACK TO IMPLEMENT THIS PROPERLY 
    public function determineQualifiers($incentive)
    {
        $settings = IncentiveSettings::first();

        $min_cap_portifolio_individual = $settings->min_cap_portifolio_individual;
        $min_cap_portifolio_group = $settings->min_cap_portifolio_group;
        $min_cap_portifolio_fast = $settings->min_cap_portifolio_fast;
        $min_cap_client_individual = $settings->min_cap_client_individual;
        $min_cap_client_group = $settings->min_cap_client_group;
        $max_par_individual = $settings->max_par_individual;
        $max_par_group = $settings->max_par_group;
        $max_par_fast = $settings->max_par_fast;
        $max_llr_group = $settings->max_llr_group;
        $max_llr_individual = $settings->max_llr_individual;
        $max_llr_fast = $settings->max_llr_fast;
        $min_cap_number_of_groups_fast = $settings->min_cap_number_of_groups_fast;

            $min_net_portfolio_growth_mse = IncentiveSettings::first()->min_net_portfolio_growth_mse;
            $min_cap_client_mse = IncentiveSettings::first()->min_cap_client_growth_mse;
            $max_par_mse = IncentiveSettings::first()->max_par_mse;
            $max_llr_mse = IncentiveSettings::first()->max_llr_mse;

        // ✅ Individual
        if (
            isset($incentive['outstanding_principal_individual']) &&
            isset($incentive['unique_customer_id_individual']) &&
            isset($incentive['records_for_PAR']) &&
            isset($incentive['monthly_loan_loss_rate'])
        ) {
            if (
                $incentive['outstanding_principal_individual'] >= $min_cap_portifolio_individual &&
                $incentive['unique_customer_id_individual'] >= $min_cap_client_individual &&
                $incentive['records_for_PAR'] <= $max_par_individual &&
                $incentive['monthly_loan_loss_rate'] <= $max_llr_individual
            ) {
                return true;
            }
        }

        // ✅ Group
        if (
            isset($incentive['outstanding_principal_group']) &&
            isset($incentive['records_for_unique_group_id_group']) &&
            isset($incentive['records_for_PAR']) &&
            isset($incentive['monthly_loan_loss_rate'])
        ) {
            if (
                $incentive['outstanding_principal_group'] >= $min_cap_portifolio_group &&
                $incentive['records_for_unique_group_id_group'] >= $min_cap_client_group &&
                $incentive['records_for_PAR'] <= $max_par_group &&
                $incentive['monthly_loan_loss_rate'] <= $max_llr_group
            ) {
                return true;
            }
        }

        // ✅ FAST
        if (
            isset($incentive['outstanding_principal_fast']) &&
            isset($incentive['fast_records']) &&
            isset($incentive['records_for_PAR']) &&
            isset($incentive['monthly_loan_loss_rate'])
        ) {
            if (
                $incentive['fast_records'] >= $min_cap_number_of_groups_fast &&
                $incentive['records_for_PAR'] <= $max_par_fast &&
                $incentive['monthly_loan_loss_rate'] <= $max_llr_fast &&
                $incentive['outstanding_principal_fast'] >= $min_cap_portifolio_fast
            ) {
                return true;
            }
        }

        //  MSE 
        if (
            isset($incentive['outstanding_principal_mse']) &&
            isset($incentive['unique_customer_id_mse']) &&
            isset($incentive['records_for_PAR']) &&
            isset($incentive['monthly_loan_loss_rate'])
        ) {
            if (
                $incentive['outstanding_principal_mse'] >= $min_cap_portifolio_individual && // reuse individual settings or make MSE-specific
                $incentive['unique_customer_id_mse'] >= $min_cap_client_individual &&
                $incentive['records_for_PAR'] <= $max_par_individual &&
                $incentive['monthly_loan_loss_rate'] <= $max_llr_individual
            ) {
                return true;
            }
        }

        return false;
    }

    // public function determineQualifiers($incentive)
    // {
    //     $min_cap_portifolio_individual = IncentiveSettings::first()->min_cap_portifolio_individual;
    //     $min_cap_portifolio_group = IncentiveSettings::first()->min_cap_portifolio_group;
    //     $min_cap_portifolio_fast = IncentiveSettings::first()->min_cap_portifolio_fast;
    //     $min_cap_client_individual = IncentiveSettings::first()->min_cap_client_individual;
    //     $min_cap_client_group = IncentiveSettings::first()->min_cap_client_group;
    //     $max_par_individual = IncentiveSettings::first()->max_par_individual;
    //     $max_par_group = IncentiveSettings::first()->max_par_group;
    //     $max_par_fast = IncentiveSettings::first()->max_par_fast;
    //     $max_llr_group = IncentiveSettings::first()->max_llr_group;
    //     $max_llr_individual = IncentiveSettings::first()->max_llr_individual;
    //     $max_llr_fast = IncentiveSettings::first()->max_llr_fast;
    //     $min_cap_number_of_groups_fast = IncentiveSettings::first()->min_cap_number_of_groups_fast;

    //     $min_net_portfolio_growth_mse = IncentiveSettings::first()->min_net_portfolio_growth_mse;
    //     $min_cap_client_mse = IncentiveSettings::first()->min_cap_client_growth_mse;
    //     $max_par_mse = IncentiveSettings::first()->max_par_mse;
    //     $max_llr_mse = IncentiveSettings::first()->max_llr_mse;

    //     //check if incentive is individual by checking for the presence of outstanding_principal_individual
    //     if (array_key_exists('outstanding_principal_individual', $incentive)) {
    //         $outstanding_principal_individual = $incentive['outstanding_principal_individual'];
    //         $unique_customer_id_individual = $incentive['unique_customer_id_individual'];
    //         $records_for_PAR = $incentive['records_for_PAR'];
    //         $monthly_loan_loss_rate = $incentive['monthly_loan_loss_rate'];

    //         //check if the staff qualifies for the incentive
    //         if ($outstanding_principal_individual >= $min_cap_portifolio_individual && $unique_customer_id_individual >= $min_cap_client_individual && $records_for_PAR <= $max_par_individual && $monthly_loan_loss_rate <= $max_llr_individual) {
    //             return true;
    //         }
    //     }

    //     //check if incentive is group by checking for the presence of outstanding_principal_group
    //     if (array_key_exists('outstanding_principal_group', $incentive)) {
    //         $outstanding_principal_group = $incentive['outstanding_principal_group'];
    //         $records_for_unique_group_id_group = $incentive['records_for_unique_group_id_group'];
    //         $records_for_PAR = $incentive['records_for_PAR'];
    //         $monthly_loan_loss_rate = $incentive['monthly_loan_loss_rate'];

    //         //check if the staff qualifies for the incentive
    //         if ($outstanding_principal_group >= $min_cap_portifolio_group && $records_for_unique_group_id_group >= $min_cap_client_group && $records_for_PAR <= $max_par_group && $monthly_loan_loss_rate <= $max_llr_group) {
    //             return true;
    //         }
    //     }

    //     if (array_key_exists('outstanding_principal_fast', $incentive)) {
    //         $records_for_PAR = $incentive['records_for_PAR'];
    //         $monthly_loan_loss_rate = $incentive['monthly_loan_loss_rate'];
    //         $number_of_groups = $incentive['fast_records'];
    //         $outstanding_principal_fast = $incentive['outstanding_principal_fast'];

    //         //check if the staff qualifies for the incentive
    //         if ($number_of_groups >= $min_cap_number_of_groups_fast && $records_for_PAR <= $max_par_fast && $monthly_loan_loss_rate <= $max_llr_fast && $outstanding_principal_fast >= $min_cap_portifolio_fast) {
    //             return true;
    //         }
    //     }

    //     // MSE incentive qualification
    //     if (array_key_exists('outstanding_principal_mse', $incentive)) {
    //         $outstanding_principal_mse = $incentive['outstanding_principal_mse'];
    //         $unique_customer_id_mse = $incentive['unique_customer_id_mse'];
    //         $records_for_PAR = $incentive['records_for_PAR'];
    //         $monthly_loan_loss_rate = $incentive['monthly_loan_loss_rate'];

    //         if (
    //             $outstanding_principal_mse >= $min_net_portfolio_growth_mse &&
    //             $unique_customer_id_mse >= $min_cap_client_mse &&
    //             $records_for_PAR <= $max_par_mse &&
    //             $monthly_loan_loss_rate <= $max_llr_mse
    //         ) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }

    public function calculateClientRetention($staffId, $lendingType)
    {
        // Detect current month in format YYYY-MM
        $currentMonth = now()->format('Y-m');

        // Decide if we’re using group_id or customer_id
        $useGroupId = in_array(strtolower($lendingType), ['group', 'fast']);

        $idColumn = $useGroupId ? 'group_id' : 'customer_id';

        // A. CURRENT CLIENTS from arrears
        $currentClients = Arrear::withoutGlobalScope(ArrearScope::class)
            ->where('staff_id', $staffId)
            ->where('lending_type', $lendingType)
            ->whereNotNull($idColumn)
            ->distinct()
            ->pluck($idColumn)
            ->count();

        // B. PREVIOUS MONTH CLIENTS from previous_end_month
        $previousClients = PreviousEndMonth::query()
            ->where('staff_id', $staffId)
            ->where('lending_type', $lendingType)
            ->whereNotNull($idColumn)
            ->distinct()
            ->pluck($idColumn)
            ->count();

        // C. NEW CLIENTS THIS MONTH (cycle 1, disbursed this month)
        $newCycle1Clients = Arrear::withoutGlobalScope(ArrearScope::class)
            ->where('staff_id', $staffId)
            ->where('lending_type', $lendingType)
            ->where('cycle', '1')
            ->where('disbursement_date', 'like', "$currentMonth%")
            ->whereNotNull($idColumn)
            ->distinct()
            ->pluck($idColumn)
            ->count();

        // Avoid divide-by-zero
        $denominator = $previousClients + $newCycle1Clients;
        if ($denominator === 0) {
            return 0;
        }

        return round($currentClients / $denominator, 4); // e.g., 0.8857
    }

    public function calculateRetentionScore($staffId, $lendingType)
    {
        $settings = IncentiveSettings::first();

        // Calculate actual retention
        $actualRetention = $this->calculateClientRetention($staffId, $lendingType);

        // Get dynamic thresholds
        $minKey = "retention_min_" . strtolower($lendingType);
        $maxKey = "retention_max_" . strtolower($lendingType);
        $weightKey = "retention_weight_" . strtolower($lendingType);

        $min = $settings->$minKey ?? 0;
        $max = $settings->$maxKey ?? 1;
        $weight = $settings->$weightKey ?? 0;

        // Calculate score
        $retentionScore = 0;
        if ($actualRetention >= $min) {
            $retentionScore = (($actualRetention - $min) / ($max - $min)) * ($weight / 100) * $settings->max_incentive;
        }

        return round($retentionScore, 2);
    }
}
