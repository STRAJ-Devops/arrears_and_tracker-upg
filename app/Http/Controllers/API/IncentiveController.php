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
    // public function calculateIncentive()
    // {
    //     $incentives = $this->getAllIncentives();
    //     $incentivesWithDetails = [];
    //     $logged_user = auth()->user()->user_type;
    //     $staff_id = auth()->user()->staff_id;

    //     if ($logged_user == 5 || $logged_user == 4) {
    //         foreach ($incentives as $staffId => $incentive) {
    //             // Get staff_id details from officers table
    //             $officer = Officer::where('staff_id', $staffId)->first();

    //             //check if officer branch_id is 1000 and just continue. this is to eliminate head office staff
    //             if ($officer->branch_id == 1000) {
    //                 continue;
    //             }
    //             if ($this->determineQualifiers($incentive)) {
    //                 if (array_key_exists('outstanding_principal_sgl', $incentive)) {
    //                     //incentive amount for PAR
    //                     $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPARSGL($incentive['records_for_PAR']);
    //                     //incentive amount for Net Portfolio Growth
    //                     $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowthSGL($incentive['net_portifolio_growth']);
    //                     //incentive amount for Net Client Growth
    //                     $incentive['incentive_number_of_sgl_groups'] = $this->calculateIncentiveAmountSGLGroups($incentive['sgl_records']);
    //                     //total incentive amount
    //                     $incentive['total_incentive_amount'] = ROUND(($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_number_of_sgl_groups']), 2);
    //                 } else {
    //                     //incentive amount for PAR
    //                     $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR']);
    //                     //incentive amount for Net Portfolio Growth
    //                     $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['net_portifolio_growth']);

    //                     //incentive amount for Net Client Growth
    //                     $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($incentive['net_client_growth']);

    //                     $incentives['sgl_records'] = 0;

    //                     //total incentive amount
    //                     $incentive['total_incentive_amount'] = ROUND(($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_amount_Net_Client_Growth']), 2);
    //                 }
    //             } else {
    //                 $incentive['incentive_amount_PAR'] = 0;
    //                 $incentive['incentive_amount_Net_Portifolio_Growth'] = 0;
    //                 $incentive['incentive_amount_Net_Client_Growth'] = 0;
    //                 $incentive['total_incentive_amount'] = 0;
    //                 $incentives['sgl_records'] = 0;
    //             }

    //             // Combine the officer details with the incentives
    //             $incentivesWithDetails[$staffId] = [
    //                 'incentive' => $incentive,
    //                 'officer_details' => $officer,
    //             ];
    //         }
    //     } else {
    //         foreach ($incentives as $staffId => $incentive) {
    //             // Get staff_id details from officers table
    //             if ($staffId == $staff_id) {
    //                 $officer = Officer::where('staff_id', $staffId)->first();
    //                 if ($this->determineQualifiers($incentive)) {

    //                     //incentive amount for PAR
    //                     $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR']);
    //                     //incentive amount for Net Portfolio Growth
    //                     $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['net_portifolio_growth']);

    //                     //incentive amount for Net Client Growth
    //                     $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($incentive['net_client_growth']);

    //                     $incentives['sgl_records'] = 0;

    //                     //total incentive amount
    //                     $incentive['total_incentive_amount'] = ROUND(($incentive['incentive_amount_PAR'] + $incentive['incentive_amount_Net_Portifolio_Growth'] + $incentive['incentive_amount_Net_Client_Growth']), 2);
    //                 } else {
    //                     $incentive['incentive_amount_PAR'] = 0;
    //                     $incentive['incentive_amount_Net_Portifolio_Growth'] = 0;
    //                     $incentive['incentive_amount_Net_Client_Growth'] = 0;
    //                     $incentive['total_incentive_amount'] = 0;
    //                     $incentives['sgl_records'] = 0;
    //                 }
    //                 // Combine the officer details with the incentives
    //                 $incentivesWithDetails[$staffId] = [
    //                     'incentive' => $incentive,
    //                     'officer_details' => $officer,
    //                 ];

    //                 //stop the loop if the staff_id is equal to the logged in user staff_id
    //                 break;
    //             }
    //         }
    //     }
    //     return response()->json(['incentives' => $incentivesWithDetails, 'message' => 'Incentives calculated successfully'], 200);

    // }

    public function calculateIncentive()
    {
        $incentives = $this->getAllIncentives();
        $incentivesWithDetails = [];
        $logged_user = auth()->user()->user_type;
        $staff_id = auth()->user()->staff_id;

        foreach ($incentives as $staffId => $incentive) {
            if ($logged_user != 5 && $logged_user != 4 && $staffId != $staff_id) {
                continue;
            }

            $officer = Officer::where('staff_id', $staffId)->first();
            if (!$officer || $officer->branch_id == 1000) {
                continue;
            }

            if ($this->determineQualifiers($incentive)) {
                if (array_key_exists('outstanding_principal_sgl', $incentive)) {
                    // FAST / SGL
                    $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPARSGL($incentive['records_for_PAR']);
                    $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowthSGL($incentive['net_portifolio_growth']);
                    $incentive['incentive_number_of_sgl_groups'] = $this->calculateIncentiveAmountSGLGroups($incentive['sgl_records']);
                    $incentive['incentive_amount_Client_Retention'] = $this->calculateIncentiveAmountClientRetentionFast($incentive['client_retention'] ?? 0);
                    $incentive['total_incentive_amount'] = ROUND((
                        $incentive['incentive_amount_PAR'] +
                        $incentive['incentive_amount_Net_Portifolio_Growth'] +
                        $incentive['incentive_number_of_sgl_groups'] +
                        $incentive['incentive_amount_Client_Retention']
                    ), 2);
                } elseif (array_key_exists('outstanding_principal_group', $incentive)) {
                    // GROUP
                    $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR']);
                    $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['net_portifolio_growth']);
                    $incentive['incentive_amount_Client_Retention'] = $this->calculateIncentiveAmountClientRetentionGroup($incentive['client_retention'] ?? 0);
                    $incentive['incentive_amount_Net_Group_Growth'] = $this->calculateIncentiveAmountNetGroupGrowth($incentive['net_client_growth'] ?? 0);
                    $incentive['total_incentive_amount'] = ROUND((
                        $incentive['incentive_amount_PAR'] +
                        $incentive['incentive_amount_Net_Portifolio_Growth'] +
                        $incentive['incentive_amount_Client_Retention'] +
                        $incentive['incentive_amount_Net_Group_Growth']
                    ), 2);
                } elseif (array_key_exists('outstanding_principal_sme', $incentive)) {
                    // SME
                    $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR']);
                    $incentive['incentive_amount_Client_Retention'] = $this->calculateIncentiveAmountClientRetentionSME($incentive['client_retention'] ?? 0);
                    $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowthSME($incentive['net_portifolio_growth'] ?? 0);
                    $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowthSME($incentive['net_client_growth'] ?? 0);
                    $incentive['total_incentive_amount'] = ROUND((
                        $incentive['incentive_amount_PAR'] +
                        $incentive['incentive_amount_Client_Retention'] +
                        $incentive['incentive_amount_Net_Portifolio_Growth'] +
                        $incentive['incentive_amount_Net_Client_Growth']
                    ), 2);
                } else {
                    // INDIVIDUAL
                    $incentive['incentive_amount_PAR'] = $this->calculateIncentiveAmountPAR($incentive['records_for_PAR']);
                    $incentive['incentive_amount_Net_Portifolio_Growth'] = $this->calculateIncentiveAmountNetPortifolioGrowth($incentive['net_portifolio_growth']);
                    $incentive['incentive_amount_Net_Client_Growth'] = $this->calculateIncentiveAmountNetClientGrowth($incentive['net_client_growth']);
                    $incentive['incentive_amount_Client_Retention'] = $this->calculateIncentiveAmountClientRetentionIndividual($incentive['client_retention'] ?? 0);
                    $incentive['total_incentive_amount'] = ROUND((
                        $incentive['incentive_amount_PAR'] +
                        $incentive['incentive_amount_Net_Portifolio_Growth'] +
                        $incentive['incentive_amount_Net_Client_Growth'] +
                        $incentive['incentive_amount_Client_Retention']
                    ), 2);
                }
            } else {
                // Doesn't qualify
                $incentive['incentive_amount_PAR'] = 0;
                $incentive['incentive_amount_Net_Portifolio_Growth'] = 0;
                $incentive['incentive_amount_Net_Client_Growth'] = 0;
                $incentive['incentive_amount_Client_Retention'] = 0;
                $incentive['incentive_amount_Net_Group_Growth'] = 0;
                $incentive['incentive_number_of_sgl_groups'] = 0;
                $incentive['total_incentive_amount'] = 0;
                $incentives['sgl_records'] = 0;
            }

            $incentivesWithDetails[$staffId] = [
                'incentive' => $incentive,
                'officer_details' => $officer,
            ];

            // Skip rest if not admin
            if ($logged_user != 5 && $logged_user != 4) break;
        }

        return response()->json(['incentives' => $incentivesWithDetails, 'message' => 'Incentives calculated successfully'], 200);
    }





    public function getAllIncentives()
    {
        $overallIndividualRecords = $this->overallIndividualRecords();
        $overallGroupRecords = $this->overallGroupRecords();
        $overallSGLRecords = $this->overallSGLRecords();
        $overallSMERecords = $this->overallSMERecords(); // NEW

        $incentives = [];

        foreach ($overallIndividualRecords as $staffId => $record) {
            $previousMonthOutstandingPrincipal = PreviousEndMonth::where('staff_id', $staffId)->sum('outsanding_principal');
            $record['previous_outstanding_principal_individual'] = $previousMonthOutstandingPrincipal;
            $record['outstanding_principal_group'] = 0;
            $record['sgl_records'] = 0;
            $record['records_for_unique_group_id_group'] = 0;

            $netPortifolioGrowth = $this->calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $record['outstanding_principal_individual']);
            $record['net_portifolio_growth'] = $netPortifolioGrowth;

            $previousMonthUniqueCustomerCount = PreviousEndMonth::where('staff_id', $staffId)
                ->where('lending_type', 'Individual')
                ->distinct()->get(['customer_id'])
                ->count('customer_id');

            $netClientGrowth = $this->calculateNetClientGrowth($previousMonthUniqueCustomerCount, $record['unique_customer_id_individual']);
            $record['net_client_growth'] = $netClientGrowth;

            $record['incentive_type'] = "individual";
            $incentives[$staffId] = $record;
        }

        foreach ($overallGroupRecords as $staffId => $record) {
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

            $record['incentive_type'] = "group";
            $incentives[$staffId] = $record;
        }

        foreach ($overallSGLRecords as $staffId => $record) {
            $previousMonthOutstandingPrincipal = PreviousEndMonth::where('staff_id', $staffId)->sum('outsanding_principal');
            $record['previous_outstanding_principal_sgl'] = $previousMonthOutstandingPrincipal;
            $record['net_portifolio_growth'] = $this->calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $record['outstanding_principal_sgl']);
            $record['net_client_growth'] = 0;
            $record['incentive_type'] = "fast";
            $incentives[$staffId] = $record;
        }

        foreach ($overallSMERecords as $staffId => $record) {
            $previousMonthOutstandingPrincipal = PreviousEndMonth::where('staff_id', $staffId)->sum('outsanding_principal');
            $record['previous_outstanding_principal_sme'] = $previousMonthOutstandingPrincipal;

            $record['outstanding_principal_individual'] = 0;
            $record['outstanding_principal_group'] = 0;
            $record['outstanding_principal_sgl'] = 0;
            $record['unique_customer_id_individual'] = 0;
            $record['records_for_unique_group_id_group'] = 0;
            $record['sgl_records'] = 0;

            $record['net_portifolio_growth'] = $this->calculateNetPortifolioGrowth($previousMonthOutstandingPrincipal, $record['outstanding_principal_sme']);
            $record['net_client_growth'] = 0;

            $record['incentive_type'] = "sme";
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
     * Individual client retention rate individual
     */
    public function calculateClientRetentionRateIndividual()
    {
        $currentMonthClients = Arrear::withoutGlobalScope(ArrearScope::class)
            ->where('lending_type', 'Individual')
            ->where('product_id', '!=', '21070')
            ->groupBy('staff_id')
            ->select('staff_id', DB::raw('COUNT(DISTINCT customer_id) as current_clients'))
            ->get()
            ->keyBy('staff_id');

        $previousMonthClients = PreviousEndMonth::where('loan_type', 'Individual')
            ->select('staff_id', DB::raw('SUM(previous_month_clients) as previous_clients'))
            ->groupBy('staff_id')
            ->get()
            ->keyBy('staff_id');

        $newClientsCycle1 = PreviousEndMonth::where('loan_type', 'Individual')
            ->select('staff_id', DB::raw('SUM(cycle_one_clients_disbursed) as new_cycle_1'))
            ->groupBy('staff_id')
            ->get()
            ->keyBy('staff_id');

        $retentionRates = [];

        foreach ($currentMonthClients as $staffId => $current) {
            $prev = $previousMonthClients[$staffId]->previous_clients ?? 0;
            $new = $newClientsCycle1[$staffId]->new_cycle_1 ?? 0;
            $denominator = $prev + $new;

            $rate = $denominator > 0 ? round(($current->current_clients / $denominator) * 100, 2) : 0;

            $retentionRates[$staffId] = $rate;
        }

        return $retentionRates;
    }

    public function calculateClientRetentionRateSME()
    {
        $currentMonthClients = Arrear::withoutGlobalScope(ArrearScope::class)
            ->where('lending_type', 'SME')
            ->groupBy('staff_id')
            ->select('staff_id', DB::raw('COUNT(DISTINCT customer_id) as current_clients'))
            ->get()
            ->keyBy('staff_id');

        $previousMonthClients = PreviousEndMonth::where('loan_type', 'SME')
            ->select('staff_id', DB::raw('SUM(previous_month_clients) as previous_clients'))
            ->groupBy('staff_id')
            ->get()
            ->keyBy('staff_id');

        $newClientsCycle1 = PreviousEndMonth::where('loan_type', 'SME')
            ->select('staff_id', DB::raw('SUM(cycle_one_clients_disbursed) as new_cycle_1'))
            ->groupBy('staff_id')
            ->get()
            ->keyBy('staff_id');

        $retentionRates = [];

        foreach ($currentMonthClients as $staffId => $current) {
            $prev = $previousMonthClients[$staffId]->previous_clients ?? 0;
            $new = $newClientsCycle1[$staffId]->new_cycle_1 ?? 0;
            $denominator = $prev + $new;

            $rate = $denominator > 0 ? round(($current->current_clients / $denominator) * 100, 2) : 0;

            $retentionRates[$staffId] = $rate;
        }

        return $retentionRates;
    }

    public function calculateIncentiveAmountClientRetentionSME($retention)
    {
        $minRetention = 70;
        $maxRetention = 100;
        $weight = 10;
        $maxIncentive = IncentiveSettings::first()->max_incentive_indv; // Or use separate SME incentive if available
        $amount = 0;

        if ($retention >= $minRetention) {
            $amount = (($retention - $minRetention) / ($maxRetention - $minRetention)) * ($weight / 100) * $maxIncentive;
        }

        return round($amount, 2);
    }



    public function calculateIncentiveAmountClientRetentionFast($retention)
    {
        $minRetention = 90;
        $maxRetention = 100;
        $weight = 25;
        $maxIncentive = IncentiveSettings::first()->max_incentive_fsg;
        $amount = 0;

        if ($retention >= $minRetention) {
            $amount = (($retention - $minRetention) / ($maxRetention - $minRetention)) * ($weight / 100) * $maxIncentive;
        }

        return round($amount, 2);
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

    public function calculateOutstandingPrincipalSME()
    {
        $outstandingPrincipalSumSME = Arrear::withoutGlobalScope(ArrearScope::class)
            ->select('staff_id', DB::raw('SUM(outsanding_principal) as count'))
            ->where('lending_type', 'SME')
            ->groupBy('staff_id')
            ->get();

        return $outstandingPrincipalSumSME;
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
    // public function overallIndividualRecords()
    // {
    //     $outstandingPrincipalIndividual = $this->calculateOutstandingPrincipalIndividual();

    //     $uniqueCustomerIDIndividual = $this->calculateUniqueCustomerIDIndividual();
    //     $recordsForPAR = $this->recordsForPARIndividual();
    //     $monthlyLoanLossRate = $this->recordsForMonthlyLoanLossRateIndividual();

    //     $overallIndividualRecords = [];

    //     foreach ($outstandingPrincipalIndividual as $record) {
    //         $staffId = $record->staff_id;
    //         $overallIndividualRecords[$staffId] = [
    //             'outstanding_principal_individual' => $record->count,
    //         ];
    //     }

    //     foreach ($uniqueCustomerIDIndividual as $record) {
    //         $staffId = $record->staff_id;
    //         if (!isset($overallIndividualRecords[$staffId])) {
    //             $overallIndividualRecords[$staffId] = [];
    //         }
    //         $overallIndividualRecords[$staffId]['unique_customer_id_individual'] = $record->count;
    //     }

    //     foreach ($recordsForPAR as $record) {
    //         $staffId = $record->staff_id;
    //         if (!isset($overallIndividualRecords[$staffId])) {
    //             $overallIndividualRecords[$staffId] = [];
    //         }
    //         $overallIndividualRecords[$staffId]['records_for_PAR'] = $record->count;
    //     }

    //     foreach ($monthlyLoanLossRate as $record) {
    //         $staffId = $record->staff_id;
    //         if (!isset($overallIndividualRecords[$staffId])) {
    //             $overallIndividualRecords[$staffId] = [];
    //         }
    //         $overallIndividualRecords[$staffId]['monthly_loan_loss_rate'] = $record->count;
    //     }

    //     //filter only those with sgl_records property or has all [outstanding_principal_individual, unique_customer_id_individual, records_for_PAR, monthly_loan_loss_rate]
    //     $overallIndividualRecords = array_filter($overallIndividualRecords, function ($record) {
    //         return isset($record['outstanding_principal_individual']) && isset($record['unique_customer_id_individual']) && isset($record['records_for_PAR']) && isset($record['monthly_loan_loss_rate']);
    //     });

    //     return $overallIndividualRecords;
    // }

    public function overallIndividualRecords()
    {
        $outstandingPrincipalIndividual = $this->calculateOutstandingPrincipalIndividual();
        $uniqueCustomerIDIndividual = $this->calculateUniqueCustomerIDIndividual();
        $recordsForPAR = $this->recordsForPARIndividual();
        $monthlyLoanLossRate = $this->recordsForMonthlyLoanLossRateIndividual();
        $retentionRates = $this->calculateClientRetentionRateIndividual();

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

        foreach ($retentionRates as $staffId => $rate) {
            if (!isset($overallIndividualRecords[$staffId])) {
                $overallIndividualRecords[$staffId] = [];
            }
            $overallIndividualRecords[$staffId]['client_retention'] = $rate;
        }

        // Filter only complete records
        $overallIndividualRecords = array_filter($overallIndividualRecords, function ($record) {
            return isset($record['outstanding_principal_individual']) &&
                isset($record['unique_customer_id_individual']) &&
                isset($record['records_for_PAR']) &&
                isset($record['monthly_loan_loss_rate']);
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

    public function overallSMERecords()
    {
        $outstandingPrincipalSME = $this->calculateOutstandingPrincipalSME();
        $recordsForPAR = $this->recordsForPARSME();
        $monthlyLoanLossRate = $this->recordsForMonthlyLoanLossRateSME();
        $retentionRates = $this->calculateClientRetentionRateSME();

        $previousMonthClients = PreviousEndMonth::where('loan_type', 'SME')
            ->select('staff_id', DB::raw('SUM(previous_month_clients) as count'))
            ->groupBy('staff_id')
            ->get()
            ->keyBy('staff_id');

        $currentMonthClients = Arrear::withoutGlobalScope(ArrearScope::class)
            ->where('lending_type', 'SME')
            ->select('staff_id', DB::raw('COUNT(DISTINCT customer_id) as count'))
            ->groupBy('staff_id')
            ->get()
            ->keyBy('staff_id');

        $overallSMERecords = [];

        foreach ($outstandingPrincipalSME as $record) {
            $staffId = $record->staff_id;
            $overallSMERecords[$staffId] = [
                'outstanding_principal_sme' => $record->count,
            ];
        }

        foreach ($recordsForPAR as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallSMERecords[$staffId])) {
                $overallSMERecords[$staffId] = [];
            }
            $overallSMERecords[$staffId]['records_for_PAR'] = $record->count;
        }

        foreach ($monthlyLoanLossRate as $record) {
            $staffId = $record->staff_id;
            if (!isset($overallSMERecords[$staffId])) {
                $overallSMERecords[$staffId] = [];
            }
            $overallSMERecords[$staffId]['monthly_loan_loss_rate'] = $record->count;
        }

        foreach ($retentionRates as $staffId => $rate) {
            if (!isset($overallSMERecords[$staffId])) {
                $overallSMERecords[$staffId] = [];
            }
            $overallSMERecords[$staffId]['client_retention'] = $rate;
        }

        foreach ($currentMonthClients as $staffId => $current) {
            $prev = $previousMonthClients[$staffId]->count ?? 0;
            $netGrowth = $current->count - $prev;

            if (!isset($overallSMERecords[$staffId])) {
                $overallSMERecords[$staffId] = [];
            }

            $overallSMERecords[$staffId]['net_client_growth'] = $netGrowth;
        }

        foreach ($overallSMERecords as $staffId => &$record) {
            $previous = PreviousEndMonth::where('staff_id', $staffId)->sum('outsanding_principal');
            $current = $record['outstanding_principal_sme'] ?? 0;
            $record['net_portifolio_growth'] = $this->calculateNetPortifolioGrowth($previous, $current);
        }

        return array_filter($overallSMERecords, function ($record) {
            return isset($record['outstanding_principal_sme']) &&
                isset($record['records_for_PAR']) &&
                isset($record['monthly_loan_loss_rate']) &&
                isset($record['net_client_growth']) &&
                isset($record['net_portifolio_growth']);
        });
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
            $overallSGLRecords[$staffId]['records_for_PAR'] = $record->count;
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


    public function calculateIncentiveAmountNetGroupGrowth($groupGrowth)
    {
        $minThreshold = 2;
        $maxThreshold = 5;
        $weight = 35;
        $maxIncentive = IncentiveSettings::first()->max_incentive_group;
        $amount = 0;

        if ($groupGrowth >= $minThreshold) {
            $amount = (($groupGrowth - $minThreshold) / ($maxThreshold - $minThreshold)) * ($weight / 100) * $maxIncentive;
        }

        return round($amount, 2);
    }


    public function calculateIncentiveAmountClientRetentionIndividual($retentionRate)
    {
        $minThreshold = 70;
        $maxThreshold = 100;
        $weight = 15;
        $maxIncentive = IncentiveSettings::first()->max_incentive_indv;
        $amount = 0;

        if ($retentionRate >= $minThreshold) {
            $amount = (($retentionRate - $minThreshold) / ($maxThreshold - $minThreshold)) * ($weight / 100) * $maxIncentive;
        }

        return round($amount, 2);
    }


    /**
     * Incentive calculations
     */

    public function calculateIncentiveAmountPAR($par)
    {
        $maxPar = IncentiveSettings::first()->max_par;
        $parPercentage = IncentiveSettings::first()->percentage_incentive_par;
        $maximumIncentive = IncentiveSettings::first()->max_incentive_indv;
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
        $maximumIncentive = IncentiveSettings::first()->max_incentive_fsg;
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
        $maximumIncentive = IncentiveSettings::first()->max_incentive_indv;
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

    public function calculateIncentiveAmountNetPortifolioGrowthSME($netGrowth)
    {
        $min = 20000000;
        $max = 70000000;
        $weight = 35; // from memo
        $maximumIncentive = IncentiveSettings::first()->max_incentive_indv; // Adjust if SME has its own cap
        $amount = 0;

        if ($netGrowth >= $min) {
            $amount = (($netGrowth - $min) / ($max - $min)) * ($weight / 100) * $maximumIncentive;
        }

        // Cap at max incentive portion
        if ($netGrowth >= $max) {
            $amount = ($weight / 100) * $maximumIncentive;
        }

        return round($amount, 2);
    }


    //net portifolio growth for SGL incentive
    public function calculateIncentiveAmountNetPortifolioGrowthSGL($outstandingPrincipalSGL)
    {
        $max = IncentiveSettings::first()->max_cap_portifolio_fast;
        $min = IncentiveSettings::first()->min_cap_portifolio_fast;
        $portifolioPercentage = 40;
        $maximumIncentive = IncentiveSettings::first()->max_incentive_fsg;
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
        $maximumIncentive = IncentiveSettings::first()->max_incentive_indv;

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
        $maximumIncentive = IncentiveSettings::first()->max_incentive_sgl;

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

    public function calculateIncentiveAmountNetClientGrowthSME($growth)
    {
        $min = 4;
        $max = 10;
        $weight = 25;
        $maxIncentive = IncentiveSettings::first()->max_incentive_indv; // Adjust if SME-specific cap is added
        $amount = 0;

        // Guard clause: ignore negative or invalid growth
        if ($growth < $min) {
            return 0;
        }

        if ($growth >= $max) {
            $amount = ($weight / 100) * $maxIncentive;
        } else {
            $amount = (($growth - $min) / ($max - $min)) * ($weight / 100) * $maxIncentive;
        }

        return round($amount, 2);
    }



    //function to determine qualifiers
    public function determineQualifiers($incentive)
    {
        $settings = IncentiveSettings::first();

        $min_cap_portifolio_individual = $settings->min_cap_portifolio_individual;
        $min_cap_portifolio_group = $settings->min_cap_portifolio_group;
        $min_cap_portifolio_sgl = $settings->min_cap_portifolio_fast;
        $min_cap_client_individual = $settings->min_cap_client_individual;
        $min_cap_client_group = $settings->min_cap_client_group;
        $min_cap_number_of_groups_sgl = $settings->min_cap_number_of_groups_fast;
        $max_par_individual = $settings->max_par_individual;
        $max_par_group = $settings->max_par_group;
        $max_par_fast = $settings->max_par_fast;
        $max_llr_individual = $settings->max_llr_individual;
        $max_llr_group = $settings->max_llr_group;
        $max_llr_fast = $settings->max_llr_fast;

        // INDIVIDUAL
        if (array_key_exists('outstanding_principal_individual', $incentive)) {
            $portfolio = $incentive['outstanding_principal_individual'];
            $clients = $incentive['unique_customer_id_individual'];
            $par = $incentive['records_for_PAR'];
            $llr = $incentive['monthly_loan_loss_rate'];

            if (
                $portfolio >= $min_cap_portifolio_individual &&
                $clients >= $min_cap_client_individual &&
                $par <= $max_par_individual &&
                $llr <= $max_llr_individual
            ) {
                return true;
            }
        }

        // GROUP
        if (array_key_exists('outstanding_principal_group', $incentive)) {
            $portfolio = $incentive['outstanding_principal_group'];
            $groups = $incentive['records_for_unique_group_id_group'];
            $par = $incentive['records_for_PAR'];
            $llr = $incentive['monthly_loan_loss_rate'];

            if (
                $portfolio >= $min_cap_portifolio_group &&
                $groups >= $min_cap_client_group &&
                $par <= $max_par_group &&
                $llr <= $max_llr_group
            ) {
                return true;
            }
        }

        // FAST / SGL
        if (array_key_exists('outstanding_principal_sgl', $incentive)) {
            $portfolio = $incentive['outstanding_principal_sgl'];
            $groups = $incentive['sgl_records'];
            $par = $incentive['records_for_PAR'];
            $llr = $incentive['monthly_loan_loss_rate'];

            if (
                $portfolio >= $min_cap_portifolio_sgl &&
                $groups >= $min_cap_number_of_groups_sgl &&
                $par <= $max_par_fast &&
                $llr <= $max_llr_fast
            ) {
                return true;
            }
        }

        // SME
        if (array_key_exists('outstanding_principal_sme', $incentive)) {
            $portfolio = $incentive['outstanding_principal_sme'] ?? 0;
            $clients = ($incentive['net_client_growth'] ?? 0) + 50; // assuming growth + base = active
            $par = $incentive['records_for_PAR'] ?? 0;
            $llr = $incentive['monthly_loan_loss_rate'] ?? 0;

            if (
                $portfolio >= 500000000 &&
                $clients >= 50 &&
                $par <= $settings->max_par &&
                $llr <= 0.17
            ) {
                return true;
            }
        }

        return false;
    }
}
