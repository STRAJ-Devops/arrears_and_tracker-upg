<?php

namespace App\Http\Controllers;

use App\Models\PreviousArrear;
use App\Models\IncentiveSettings;
use App\Models\Officer;
use App\Models\PreviousArrearEndMonth;
use App\Models\Scopes\PreviousArrearScope;
use Illuminate\Http\Request;
//import ArrerScope
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PreviousIncentiveController extends Controller
{
    public function index()
    {
        $logged_user = auth()->user()->user_type;

        return view('previous-incentives', compact('logged_user'));
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

                if (!$officer) {
                    Log::error("PreviousIncentiveController → Missing officer for staff_id: {$staffId}");
                    continue;
                }

                //check if officer branch_id is 1000 and just continue. this is to eliminate head office staff
                if ($officer->branch_id == 1000) {
                    continue;
                }
                // Log::info("PRE-QUALIFICATION METRICS for staff {$staffId}", [
                //     'type' => $incentive['incentive_type'] ?? 'unknown',
                //     'loan_portfolio' => $incentive['outstanding_principal'] ?? $incentive['outstanding_principal'] ?? $incentive['outstanding_principal'] ?? 'N/A',
                //     'active_clients' => $incentive['f'] ?? $incentive['unique_customer_id'] ?? $incentive['records_for_unique_group_id_group'] ?? 'N/A',
                //     'PAR' => $incentive['records_for_PAR'] ?? 'N/A',
                //     'LLR' => $incentive['monthly_loan_loss_rate'] ?? 'N/A',
                //     'retention' => $this->calculateClientRetention($staffId, $incentive['incentive_type'] ?? 'unknown'),
                //     'net_portfolio_growth' => $incentive['net_portifolio_growth'] ?? 'N/A',
                //     'net_client_growth' => $incentive['net_client_growth'] ?? 'N/A',
                // ]);
                if ($this->determineQualifiers($incentive)) {
                    if ($incentive['incentive_type'] === 'fast') {
                        $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR'], $incentive['incentive_type']);
                        // $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['net_portifolio_growth'], $incentive['incentive_type']);
                        $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($incentive['net_client_growth'], $incentive['incentive_type']);
                        $incentive['client_retention'] = $this->calculateClientRetention($staffId, $incentive['incentive_type']);
                        $incentive['incentive_retention_score'] = $this->calculateRetentionScore($incentive['incentive_type'], $incentive['client_retention']);
                        $incentive['total_incentive_amount'] = $this->totalIncentiveAmount($incentive);
                        // $incentive['total_incentive_amount'] = ROUND(($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Client_Growth'] + $incentive['incentive_retention_score']), 2);

                    } elseif ($incentive['incentive_type'] === 'mse') {
                        $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR'], $incentive['incentive_type']);
                        $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['net_portifolio_growth'], $incentive['incentive_type']);
                        $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($incentive['net_client_growth'], $incentive['incentive_type']);
                        $incentive['client_retention'] = $this->calculateClientRetention($staffId, $incentive['incentive_type']);
                        $incentive['incentive_retention_score'] = $this->calculateRetentionScore($incentive['incentive_type'], $incentive['client_retention']);
                        $incentive['total_incentive_amount'] = $this->totalIncentiveAmount($incentive);
                        // $incentive['total_incentive_amount'] = round($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_amount_Net_Client_Growth'] + $incentive['incentive_retention_score'],2 );
                    } elseif ($incentive['incentive_type'] === 'group') {
                        $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR'], $incentive['incentive_type']);
                        $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['net_portifolio_growth'], $incentive['incentive_type']);
                        $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($incentive['net_client_growth'], $incentive['incentive_type']);
                        $incentive['client_retention'] = $this->calculateClientRetention($staffId, $incentive['incentive_type']);
                        $incentive['incentive_retention_score'] = $this->calculateRetentionScore($incentive['incentive_type'], $incentive['client_retention']);
                        $incentive['total_incentive_amount'] = $this->totalIncentiveAmount($incentive);
                        // $incentive['total_incentive_amount'] = round($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_amount_Net_Client_Growth'] + $incentive['incentive_retention_score'], 2);
                    } else {
                        $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR'], $incentive['incentive_type']);
                        $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['net_portifolio_growth'], $incentive['incentive_type']);
                        $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($incentive['net_client_growth'], $incentive['incentive_type']);
                        $incentive['client_retention'] = $this->calculateClientRetention($staffId, $incentive['incentive_type']);
                        $incentive['incentive_retention_score'] = $this->calculateRetentionScore($incentive['incentive_type'], $incentive['client_retention']);
                        $incentive['total_incentive_amount'] = $this->totalIncentiveAmount($incentive);
                        // $incentive['total_incentive_amount'] = round($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_amount_Net_Client_Growth'] + $incentive['incentive_retention_score'], 2);
                    }
                } else {
                    // $incentive['incentive_amount_PAR'] = 0;
                    // $incentive['incentive_amount_Net_Portifolio_Growth'] = 0;
                    // $incentive['incentive_amount_Net_Client_Growth'] = 0;
                    // $incentive['incentive_retention_score'] = 0;
                    // $incentive['total_incentive_amount'] = 0;
                    // $incentive['client_retention'] = 0;
                    // $incentives['fast_records'] = 0;

                    $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR'], $incentive['incentive_type']);
                    $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['net_portifolio_growth'], $incentive['incentive_type']);
                    $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($incentive['net_client_growth'], $incentive['incentive_type']);
                    $incentive['client_retention'] = $this->calculateClientRetention($staffId, $incentive['incentive_type']);
                    $incentive['incentive_retention_score'] = $this->calculateRetentionScore($incentive['incentive_type'], $incentive['client_retention']);
                    $incentive['total_incentive_amount'] = 0;
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
                        $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR'], $incentive['incentive_type']);
                        $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['net_portifolio_growth'], $incentive['incentive_type']);
                        $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($incentive['net_client_growth'], $incentive['incentive_type']);
                        $incentive['client_retention'] = $this->calculateClientRetention($staffId, $incentive['incentive_type']);
                        $incentive['incentive_retention_score'] = $this->calculateRetentionScore($incentive['incentive_type'], $incentive['client_retention']);
                        $incentive['total_incentive_amount'] = $this->totalIncentiveAmount($incentive);

                        // $incentive['total_incentive_amount'] = round($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_amount_Net_Client_Growth'] + $incentive['incentive_retention_score'], 2);
                    } else {
                        // $incentive['incentive_amount_PAR'] = 0;
                        // $incentive['incentive_amount_Net_Portifolio_Growth'] = 0;
                        // $incentive['incentive_amount_Net_Client_Growth'] = 0;
                        // $incentive['incentive_retention_score'] = 0;
                        // $incentive['total_incentive_amount'] = 0;
                        // $incentive['client_retention'] = 0;
                        // $incentives['fast_records'] = 0;

                        $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR'], $incentive['incentive_type']);
                        $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['net_portifolio_growth'], $incentive['incentive_type']);
                        $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($incentive['net_client_growth'], $incentive['incentive_type']);
                        $incentive['client_retention'] = $this->calculateClientRetention($staffId, $incentive['incentive_type']);
                        $incentive['incentive_retention_score'] = $this->calculateRetentionScore($incentive['incentive_type'], $incentive['client_retention']);
                        $incentive['total_incentive_amount'] = 0;
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
             * from PreviousArrearEndMonth Model
             */
            $previousMonthOutstandingPrincipal = PreviousArrearEndMonth::where('staff_id', $staffId)->sum('outsanding_principal');
            $record['previous_outstanding_principal'] = $previousMonthOutstandingPrincipal;
            $netPortifolioGrowth = $this->calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $record['outstanding_principal']);
            $record['net_portifolio_growth'] = $netPortifolioGrowth;

            $previousMonthUniqueCustomerCount = PreviousArrearEndMonth::where('staff_id', $staffId)
                ->where('lending_type', 'Individual')
                ->distinct()->get(['customer_id'])
                ->count();


            $netClientGrowth = $this->calculateNetClientGrowth($previousMonthUniqueCustomerCount, $record['unique_customer_id']);
            $record['net_client_growth'] = $netClientGrowth;

            //add a flag that indicates the record is for individual
            $record['incentive_type'] = "individual";

            $incentives[$staffId] = $record;
        }

        foreach ($overallGroupRecords as $staffId => $record) {
            /**
             * add net portifolio growth and net client growth to the record
             * from PreviousArrearEndMonth Model
             */
            $previousMonthOutstandingPrincipal = PreviousArrearEndMonth::where('staff_id', $staffId)->sum('outsanding_principal');
            $record['previous_outstanding_principal'] = $previousMonthOutstandingPrincipal;
            $netPortifolioGrowth = $this->calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $record['outstanding_principal']);
            $record['net_portifolio_growth'] = $netPortifolioGrowth;

            // Log::debug("overallGroupRecords ===>>> ", [
            //     'netPortifolioGrowth' => $netPortifolioGrowth,
            //     'PREV OUTS PRIN' => $previousMonthOutstandingPrincipal,
            //     '   CURRENT OUTS PRINCAL' => $record['outstanding_principal'],
            //     'staffId' => $staffId,
            // ]);

            $previousMonthUniqueCustomerCount = PreviousArrearEndMonth::where('staff_id', $staffId)
                ->where('lending_type', 'Group')
                ->distinct()->get(['group_id'])
                ->count();

            // Log::debug("overallGroupRecords ===>>> ", [
            //     'previousMonthUniqueCustomerCount' => $previousMonthUniqueCustomerCount,
            //     'UNIQUE CUSTOMER COUNT' => $record['unique_customer_id'],
            //     'staffId' => $staffId,
            // ]);

            $netClientGrowth = $this->calculateNetClientGrowth($previousMonthUniqueCustomerCount, $record['unique_customer_id']);
            $record['net_client_growth'] = $netClientGrowth;

            //add a flag that indicates the record is for group
            $record['incentive_type'] = "group";
            $incentives[$staffId] = $record;
        }

        foreach ($overallFASTRecords as $staffId => $record) {
            $previousMonthOutstandingPrincipal = PreviousArrearEndMonth::where('staff_id', $staffId)->sum('outsanding_principal');
            $record['previous_outstanding_principal'] = $previousMonthOutstandingPrincipal;
            // $netPortifolioGrowth = $this->calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $record['outstanding_principal']);
            $netPortifolioGrowth = $this->calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $record['outstanding_principal'] ?? 0);
            $record['net_portifolio_growth'] = $netPortifolioGrowth;

            $previousMonthUniqueCustomerCount = PreviousArrearEndMonth::where('staff_id', $staffId)
                ->where('lending_type', 'fast')
                ->distinct()->get(['customer_id'])
                ->count();

            $netClientGrowth = $this->calculateNetClientGrowth($previousMonthUniqueCustomerCount, $record['unique_customer_id']);
            $record['net_client_growth'] = $netClientGrowth;

            //FOR FAST GROUP - NET CLIENT GROWTH IS ACTUAL NO OF GROUPS IN CURRENT MONTH 
            // $record['net_client_growth'] = $record['unique_customer_id'];
            //add a flag that indicates the record is for fast
            $record['incentive_type'] = "fast";
            $incentives[$staffId] = $record;
        }



        foreach ($overallMSERecords as $staffId => $record) {
            $previousMonthOutstandingPrincipal = PreviousArrearEndMonth::where('staff_id', $staffId)->sum('outsanding_principal');
            $record['previous_outstanding_principal'] = $previousMonthOutstandingPrincipal;
            $netPortifolioGrowth = $this->calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $record['outstanding_principal']);
            $record['net_portifolio_growth'] = $netPortifolioGrowth;

            $previousMonthUniqueCustomerCount = PreviousArrearEndMonth::where('staff_id', $staffId)
                ->where('lending_type', 'mse')
                ->distinct()->get(['customer_id'])
                ->count();

            $netClientGrowth = $this->calculateNetClientGrowth($previousMonthUniqueCustomerCount, $record['unique_customer_id']);
            $record['net_client_growth'] = $netClientGrowth;

            $record['incentive_type'] = 'mse';

            $incentives[$staffId] = $record;
        }
        ////Log::debug('Incentives with details:', $incentives);
        return $incentives;
    }



    // NEW IMPLEMENTATIONS FOR INCENTIVES
    public function calculateOutstandingPrincipal($lendingType)
    {
        return PreviousArrear::withoutGlobalScope(PreviousArrearScope::class)
            ->select('staff_id', DB::raw('SUM(outsanding_principal) as count'))
            ->where('lending_type', $lendingType)
            ->groupBy('staff_id')
            ->get();
    }

    public function calculateUniqueCustomerID($lendingType)
    {
        //group by staff_id by calculating the number of unique customer_id
        $uniqueCustomerIDIndividual = PreviousArrear::withoutGlobalScope(PreviousArrearScope::class)->select('staff_id', DB::raw('COUNT(DISTINCT customer_id) as count'))
            ->where('lending_type', $lendingType)
            ->groupBy('staff_id')
            ->get();

        if ($lendingType === 'Group') {
            $uniqueCustomerIDIndividual = PreviousArrear::withoutGlobalScope(PreviousArrearScope::class)->select('staff_id', DB::raw('COUNT(DISTINCT group_id) as count'))
                ->where('lending_type', $lendingType)
                ->groupBy('staff_id')
                ->get();
        }

        return $uniqueCustomerIDIndividual;
    }

    public function recordsForPAR($lendingType)
    {
        // Retrieve staff_id and PAR percentage directly from raw SQL query, rounded to 1 decimal place
        $recordsForPAR = PreviousArrear::withoutGlobalScope(PreviousArrearScope::class)
            ->where('lending_type', $lendingType)
            ->selectRaw('staff_id, ROUND(SUM(par) / SUM(outsanding_principal) * 100, 2) as count')
            ->groupBy('staff_id')
            ->get();

        return $recordsForPAR;
    }

    public function recordsForMonthlyLoanLossRate($lendingType)
    {
        // Calculate the monthly loan loss rate for each staffss
        $monthlyLoanLossRate = PreviousArrear::withoutGlobalScope(PreviousArrearScope::class)
            // ->where('lending_type', $lendingType)
            ->selectRaw('staff_id, round((SUM(CASE WHEN number_of_days_late > 180 THEN outsanding_principal ELSE 0 END) / SUM(outsanding_principal)) * 100, 2) as count')
            ->groupBy('staff_id')
            ->get();

        return $monthlyLoanLossRate;
    }


    public function calculateIncentiveAmountPAR($par, $lendingType)
    {
        $settings = IncentiveSettings::first();
        $maxConcat = 'max_par_' . strtolower($lendingType);
        $maxIncentiveConcat = 'max_incentive_' . strtolower($lendingType);
        $percentageConcat = 'percentage_par_' . strtolower($lendingType);
        $maxPar = $settings->$maxConcat;
        $parPercentage = $settings->$percentageConcat;
        $maximumIncentive = $settings->$maxIncentiveConcat;

        $amount = ((($maxPar / 100) - ($par / 100)) / ($maxPar / 100)) * ($parPercentage / 100) * $maximumIncentive;

        $amount = ((($maxPar / 100) - ($par / 100)) / ($maxPar / 100)) * ($parPercentage / 100) * $maximumIncentive;

        // Log::debug("PAR Calculation", [
        //     'maxPar' => $maxPar,
        //     'par' => $par,
        //     'parPercentage' => $parPercentage,
        //     'maximumIncentive' => $maximumIncentive,
        //     'amount' => $amount,
        // ]);

        return ROUND($amount, 2);
    }

    public function calculateIncentiveAmountNetPortifolioGrowth($outstandingPrincipal, $lendingType)
    {

        $settings = IncentiveSettings::first();
        $maxConcat = 'max_net_portfolio_growth_' . strtolower($lendingType);
        $minConcat = 'min_net_portfolio_growth_' . strtolower($lendingType);
        $percentageConcat = 'percentage_amount_growth_' . strtolower($lendingType);
        $maxIncentiveConcat = 'max_incentive_' . strtolower($lendingType);

        $max = $settings->$maxConcat;
        $min = $settings->$minConcat;
        $portifolioPercentage = $settings->$percentageConcat;
        $maximumIncentive = $settings->$maxIncentiveConcat;
        $actual = $outstandingPrincipal;

        $amount = (($actual - $min) / ($max - $min)) * ($portifolioPercentage / 100) * $maximumIncentive;

        // Log::debug("Portfolio Growth Calculation", [
        //     'actual' => $actual,
        //     'min' => $min,
        //     'max' => $max,
        //     'portfolioPercentage' => $portifolioPercentage,
        //     'maximumIncentive' => $maximumIncentive,
        //     'amount' => $amount,
        // ]);


        return ROUND($amount, 2);
    }

    public function calculateIncentiveAmountNetClientGrowth($numberOfClient, $lendingType)
    {

        $settings = IncentiveSettings::first();
        $maxConcat = 'max_cap_client_' . strtolower($lendingType);
        $minConcat = 'min_cap_client_' . strtolower($lendingType);
        $percentageConcat = 'percentage_client_growth_' . strtolower($lendingType);
        $maxIncentiveConcat = 'max_incentive_' . strtolower($lendingType);

        $max = $settings->$maxConcat;
        $min = $settings->$minConcat;
        $clientPercentage = $settings->$percentageConcat;
        $maximumIncentive = $settings->$maxIncentiveConcat;

        $actual = $numberOfClient;

        $amount = (($actual - $min) / ($max - $min)) * ($clientPercentage / 100) * $maximumIncentive;

        // Log::debug("Net Client Growth Calculation", [
        //     'actual' => $actual,
        //     'min' => $min,
        //     'max' => $max,
        //     'clientPercentage' => $clientPercentage,
        //     'maximumIncentive' => $maximumIncentive,
        //     'amount' => $amount,
        // ]);

        return ROUND($amount, 2);
    }

    public function calculateClientRetention($staffId, $lendingType)
    {
        // Detect current month in format 'M-y' matching your DB date format like 'Aug-25'
        $currentMonth = now()->format('M-y');

        $column = (strtolower($lendingType) === 'group') ? 'group_id' : 'customer_id';

        // A. CURRENT CLIENTS from arrears
        $currentClients = PreviousArrear::withoutGlobalScope(PreviousArrearScope::class)
            ->where('staff_id', $staffId)
            ->where('lending_type', $lendingType)
            ->whereNotNull('staff_id')
            ->distinct($column)
            ->count($column);
        // Log::debug("Current Clients for staff_id {$staffId}: {$currentClients}");

        // B. PREVIOUS MONTH CLIENTS from previous_end_month
        $previousClients = PreviousArrearEndMonth::query()
            ->where('staff_id', $staffId)
            ->where('lending_type', $lendingType)
            ->whereNotNull('staff_id')
            ->distinct($column)
            ->count($column);
        // Log::debug("Previous Clients for staff_id {$staffId}: {$previousClients}");

        // C. NEW CLIENTS THIS MONTH (cycle 1, disbursed this month)
        $newCycle1Clients = PreviousArrear::withoutGlobalScope(PreviousArrearScope::class)
            ->where('staff_id', $staffId)
            ->where('lending_type', $lendingType)
            ->whereNotNull('staff_id')
            ->where('cycle', '1')
            ->where('disbursement_date', 'like', "%-$currentMonth")
            ->distinct($column)
            ->count($column);
        // Log::debug("New Cycle 1 Clients for staff_id {$staffId} and month {$currentMonth}: {$newCycle1Clients}");

        // Avoid divide-by-zero
        $denominator = $previousClients + $newCycle1Clients;
        // Log::debug("Denominator (Previous + New Cycle1) for staff_id {$staffId}: {$denominator}");

        if ($denominator === 0) {
            //Log::debug("Denominator is zero for staff_id {$staffId}, returning 0");
            return 1;
        }

        $retentionRatio = round(($currentClients / $denominator), 2);
        // Log::debug("Retention ratio for staff_id {$staffId}: {$retentionRatio}");

        // Log::debug("Retention Calculation", [
        //     'staff_id' => $staffId,
        //     'currentClients' => $currentClients,
        //     'previousClients' => $previousClients,
        //     'newCycle1Clients' => $newCycle1Clients,
        //     'denominator' => $denominator,
        //     'retentionRatio' => ($denominator === 0 ? 0 : round(($currentClients / $denominator), 2)),
        //     'month' => $currentMonth,
        //     'lendingType' => $lendingType,
        // ]);


        return $retentionRatio;
    }


    public function calculateRetentionScore($lendingType, $actualRetention)
    {
        $settings = IncentiveSettings::first();

        $minKey = "retention_min_" . strtolower($lendingType);
        $maxKey = "retention_max_" . strtolower($lendingType);
        $weightKey = "percentage_client_retention_" . strtolower($lendingType);

        $maxIncentiveConcat = 'max_incentive_' . strtolower($lendingType);

        $maximumIncentive = $settings->$maxIncentiveConcat;

        $min = ($settings->$minKey ?? 0) / 100;  // 90 => 0.9
        $max = ($settings->$maxKey ?? 100) / 100; // 100 => 1.0

        $weight = $settings->$weightKey ?? 0;

        $actualRetention = round(($actualRetention ?? 0), 2);

        $retentionScore = round((($actualRetention - $min) / ($max - $min)) * ($weight / 100) * $maximumIncentive, 2);


        // Log::debug("calculateRetentionScore inputs:", [
        //     'lendingType' => $lendingType,
        //     'actualRetention' => $actualRetention,
        //     'minThreshold' => $min,
        //     'maxThreshold' => $max,
        //     'weightPercent' => $weight,
        //     'retentionScore' => $retentionScore,
        //     'maxIncentive' => $maximumIncentive,
        // ]);

        return round($retentionScore, 2);
    }

    public function overallIndividualRecords()
    {
        $outstandingPrincipalIndividual = $this->calculateOutstandingPrincipal('Individual');

        $uniqueCustomerIDIndividual = $this->calculateUniqueCustomerID('Individual');
        $recordsForPAR = $this->recordsForPAR('Individual');
        $monthlyLoanLossRate = $this->recordsForMonthlyLoanLossRate('Individual');

        $overallIndividualRecords = [];

        foreach ($outstandingPrincipalIndividual as $record) {
            $staffId = $record->staff_id;
            $overallIndividualRecords[$staffId] = [
                'outstanding_principal' => $record->count,
            ];
        }

        foreach ($uniqueCustomerIDIndividual as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallIndividualRecords[$staffId])) {
                $overallIndividualRecords[$staffId] = [];
            }
            $overallIndividualRecords[$staffId]['unique_customer_id'] = $record->count;
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

        //filter only those with fast_records property or has all [outstanding_principal_individual, unique_customer_id, records_for_PAR, monthly_loan_loss_rate]
        $overallIndividualRecords = array_filter($overallIndividualRecords, function ($record) {
            return isset($record['outstanding_principal']) && isset($record['unique_customer_id']) && isset($record['records_for_PAR']) && isset($record['monthly_loan_loss_rate']);
        });

        return $overallIndividualRecords;
    }

    public function overallGroupRecords()
    {
        $outstandingPrincipalGroup = $this->calculateOutstandingPrincipal('Group');
        $recordsForUniqueGroupIDGroup = $this->calculateUniqueCustomerID('Group');
        $recordsForPAR = $this->recordsForPAR('Group');
        $monthlyLoanLossRate = $this->recordsForMonthlyLoanLossRate('Group');

        $overallGroupRecords = [];

        foreach ($outstandingPrincipalGroup as $record) {
            $staffId = $record->staff_id;
            $overallGroupRecords[$staffId] = [
                'outstanding_principal' => $record->count,
            ];
        }

        foreach ($recordsForUniqueGroupIDGroup as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallGroupRecords[$staffId])) {
                $overallGroupRecords[$staffId] = [];
            }
            $overallGroupRecords[$staffId]['unique_customer_id'] = $record->count;
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

    public function overallFASTRecords()
    {
        $recordsForNoOfGroupsPAR = $this->recordsForPAR('Fast');
        $recordsForMonthlyLoanLossRateGroup = $this->recordsForMonthlyLoanLossRate('Fast');
        $recordsForNoOfGroupCustomer = $this->calculateUniqueCustomerID('Fast');
        $outstandingPrincipalFAST = $this->calculateOutstandingPrincipal('Fast');

        $overallFASTRecords = [];

        foreach ($outstandingPrincipalFAST as $record) {
            $staffId = $record->staff_id;
            $staffId = $record->staff_id;
            if (!isset($overallFASTRecords[$staffId])) {
                $overallFASTRecords[$staffId] = [];
            }
            $overallFASTRecords[$staffId]['outstanding_principal'] = $record->count;
        }

        foreach ($recordsForNoOfGroupsPAR as $record) {
            $staffId = $record->staff_id;
            $staffId = $record->staff_id;
            if (!isset($overallFASTRecords[$staffId])) {
                $overallFASTRecords[$staffId] = [];
            }
            $overallFASTRecords[$staffId]['records_for_PAR'] = $record->count;
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
            $overallFASTRecords[$staffId]['unique_customer_id'] = $record->count;
        }

        // //filter only those with fast_records property or has all [recordsForNoOfGroupsPAR, recordsForMonthlyLoanLossRateGroup, recordsForNoOfGroupCustomer]
        // $overallFASTRecords = array_filter($overallFASTRecords, function ($record) {
        //     return isset($record['recordsForNoOfGroupsPAR']) && isset($record['recordsForMonthlyLoanLossRateGroup']) && isset($record['recordsForNoOfGroupCustomer']);
        // });

        return $overallFASTRecords;
    }

    public function overallMSERecords()
    {
        $outstandingPrincipalMSE = $this->calculateOutstandingPrincipal('mse');

        $uniqueCustomerIDMSE = $this->calculateUniqueCustomerID('mse');

        $recordsForPAR = $this->recordsForPAR('mse'); // already defined
        $monthlyLoanLossRate = $this->recordsForMonthlyLoanLossRate('mse');

        $overallMSERecords = [];

        // Log::info('overallFASTRecords Records:', $outstandingPrincipalMSE);
        // Log::info('overallFASTRecords Records:', $uniqueCustomerIDMSE);
        // Log::info('overallFASTRecords Records:', $recordsForPAR);
        // Log::info('overallFASTRecords Records:', $monthlyLoanLossRate);


        //Log::debug('MSE Outstanding Principals', $outstandingPrincipalMSE->toArray());
        //Log::debug('MSE Unique Customers', $uniqueCustomerIDMSE->toArray());
        //Log::debug('MSE recordsForPAR', $recordsForPAR->toArray());
        //Log::debug('MSE monthlyLoanLossRate', $monthlyLoanLossRate->toArray());



        foreach ($outstandingPrincipalMSE as $record) {
            $staffId = $record->staff_id;
            $overallMSERecords[$staffId] = [
                'outstanding_principal' => $record->count,
            ];
        }

        foreach ($uniqueCustomerIDMSE as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallMSERecords[$staffId])) {
                $overallMSERecords[$staffId] = [];
            }
            $overallMSERecords[$staffId]['unique_customer_id'] = $record->count;
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
            return isset($record['outstanding_principal']) &&
                isset($record['unique_customer_id']) &&
                isset($record['records_for_PAR']) &&
                isset($record['monthly_loan_loss_rate']);
        });


        return $overallMSERecords;
    }

    public function calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $currentMonthOutstandingPrincipal)
    {
        $netPortifolioGrowth = $currentMonthOutstandingPrincipal - $previousMonthOutstandingPrincipal;

        // Log::debug("Net Portfolio Growth Calculation", [
        //     'currentMonthOutstandingPrincipal' => $currentMonthOutstandingPrincipal,
        //     'previousMonthOutstandingPrincipal' => $previousMonthOutstandingPrincipal,
        //     'netPortifolioGrowth' => $netPortifolioGrowth,
        // ]);

        return $netPortifolioGrowth;
    }


    public function calculateNetClientGrowth($previousMonthUniqueCustomerID, $currentMonthUniqueCustomerID)
    {
        $netClientGrowth = $currentMonthUniqueCustomerID - $previousMonthUniqueCustomerID;
        // Log::debug("Net Client Growth Calculation", [
        //     'currentMonthUniqueCustomerID' => $currentMonthUniqueCustomerID,
        //     'previousMonthUniqueCustomerID' => $previousMonthUniqueCustomerID,
        //     'netClientGrowth' => $netClientGrowth,
        // ]);
        return $netClientGrowth;
    }


    public function settings()
    {
        $incentiveSettings = IncentiveSettings::first();
        return view('incentive-settings', compact('incentiveSettings'));
    }

    // public function update_incentive_settings(Request $request)
    // {
    //     $requestData = $request->all();
    //     $incentiveSettings = IncentiveSettings::first();

    //     // Remove commas from numeric fields
    //     $numericFields = [
    //         'max_cap_portifolio',
    //         'min_cap_portifolio',
    //         'max_cap_client',
    //         'min_cap_client',
    //         'max_incentive',
    //         'max_cap_portifolio_individual',
    //         'max_cap_portifolio_group',
    //         'min_cap_client_individual',
    //         'min_cap_client_group',
    //         'max_par_individual',
    //     ];
    //     foreach ($numericFields as $field) {
    //         if (isset($requestData[$field])) {
    //             $requestData[$field] = str_replace(',', '', $requestData[$field]);
    //         }
    //     }

    //     if ($incentiveSettings) {
    //         $incentiveSettings->update($requestData);
    //     } else {
    //         IncentiveSettings::create($requestData);
    //     }
    //     // Add a session flash message to indicate success
    //     session()->flash('success', 'Incentive settings updated successfully!');

    //     // Redirect back to the settings page
    //     return redirect()->route('incentives-settings');
    // }

    public function update_incentive_settings(Request $request)
    {
        $requestData = $request->all();
        $incentiveSettings = IncentiveSettings::first();

        // Get fillable fields from the model
        $fillable = (new IncentiveSettings)->getFillable();

        // Clean commas for numeric-looking fields in request
        foreach ($fillable as $field) {
            if (isset($requestData[$field]) && is_string($requestData[$field])) {
                if (preg_match('/^\d{1,3}(,\d{3})*(\.\d+)?$/', $requestData[$field])) {
                    $requestData[$field] = str_replace(',', '', $requestData[$field]);
                }
            }
        }

        // Save or update
        if ($incentiveSettings) {
            $incentiveSettings->update($requestData);
        } else {
            IncentiveSettings::create($requestData);
        }

        session()->flash('success', 'Incentive settings updated successfully!');
        return redirect()->route('incentives-settings');
    }



    public function determineQualifiers($incentive)
    {
        $settings = IncentiveSettings::first();
        $lendingType = strtolower($incentive['incentive_type']);

        $minCapPortfolio = 'min_cap_portifolio_' . $lendingType;
        $minCapClient = 'min_active_client_' . $lendingType;
        $maxPar = 'max_par_' . $lendingType;
        $maxLLR = 'max_llr_' . $lendingType;

        return (
            isset($incentive['outstanding_principal']) && $incentive['outstanding_principal'] >= $settings->$minCapPortfolio &&
            isset($incentive['unique_customer_id']) && $incentive['unique_customer_id'] >= $settings->$minCapClient &&
            isset($incentive['records_for_PAR']) && $incentive['records_for_PAR'] <= $settings->$maxPar &&
            isset($incentive['monthly_loan_loss_rate']) && $incentive['monthly_loan_loss_rate'] <= $settings->$maxLLR
        );
    }

    private function totalIncentiveAmount(array $incentive): float
    {
        $incentiveType = $incentive['incentive_type'];
        // Ensure keys exist; missing ones default to 0
        $par        = (float) ($incentive['incentive_amount_PAR'] ?? 0);
        if ($incentive['incentive_type'] === 'fast') {
            $npGrowth   = 0;
        } else {
            $npGrowth   = (float) ($incentive['incentive_amount_Net_Portifolio_Growth'] ?? 0);
        }
        // $npGrowth   = (float) ($incentive['incentive_amount_Net_Portifolio_Growth'] ?? 0);
        $ncGrowth   = (float) ($incentive['incentive_amount_Net_Client_Growth'] ?? 0);
        $retention  = (float) ($incentive['incentive_retention_score'] ?? 0);

        $rawTotal = $par + $npGrowth + $ncGrowth + $retention;

        $settings = IncentiveSettings::first();
        $type = strtolower($incentive['incentive_type'] ?? '');

        $maxIncentive = $type ? 'max_incentive_' . $type : null;

        if ($rawTotal > $settings->$maxIncentive) {
            return $settings->$maxIncentive;
        }

        if ($rawTotal < 0) {
            return 0.00;
        }

        return $rawTotal;

        // Log::debug("Incentive breakdown", [
        //     'NP Growth' => $npGrowth,
        //     'NC Growth' => $ncGrowth,
        //     'Retention' => $retention,
        //     'Raw Total' => $rawTotal,
        //     'par' => $par,
        //     'incentiveType' => $incentiveType,
        // ]);

        return round($rawTotal, 2);
    }
}
