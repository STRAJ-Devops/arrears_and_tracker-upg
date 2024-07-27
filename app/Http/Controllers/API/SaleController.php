<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function group_by(Request $request)
    {
        $currentMonthYear = DB::table('upload_date')->latest()->value('upload_date')??date('M-y');
        try {
            if ($request->has('group')) {
                if ($request->group == 'branches-loans' || $request->group == 'branches-clients') {
                    //sales categorized by branches
                    $sales = Sale::where('disbursement_date', 'LIKE', "%$currentMonthYear%")->get()->groupBy('branch_id');
                    //process the sales data and return the view
                    $data = [];
                    foreach ($sales as $key => $sale) {
                        //i want region_name, branch_name, and the total disbursement_amount
                        $region_name = $sale->first()->region->region_name ?? 'unknown';
                        $branch_name = $sale->first()->branch->branch_name ?? 'unknown';
                        //target_amount
                        $target_amount = $sale->first()->branch->branchTarget->target_amount ?? 0;
                        $target_clients = $sale->first()->branch->branchTarget->target_numbers ?? 0;
                        $total_disbursement_amount = $sale->sum('disbursement_amount');
                        $actual_clients = $sale->sum('number_of_group_members');
                        //balance
                        $balance_loans = $target_amount - $total_disbursement_amount;

                        //balance clients
                        $balance_clients = $target_clients - $actual_clients;

                        //actual balance
                        $balance = $request->group == 'branches-loans' ? $balance_loans : $balance_clients;
                        //%centage score for loans
                        if ($target_amount == 0) {
                            $percentage = 0;
                        } else {
                            $percentage = ($total_disbursement_amount / $target_amount) * 100;
                        }

                        //%centage score for clients
                        if ($target_clients == 0) {
                            $percentage_clients = 0;
                        } else {
                            $percentage_clients = ($actual_clients / $target_clients) * 100;
                        }

                        //assign %age score to the data array based on the group
                        $score = $request->group == 'branches-loans' ? round($percentage, 0) : round($percentage_clients, 0);

                        $data[] = [
                            'region_name' => $region_name,
                            'branch_name' => $branch_name,
                            'total_disbursement_amount' => $total_disbursement_amount,
                            'target_amount' => $target_amount,
                            'balance' => $balance,
                            'target_clients' => $target_clients,
                            'actual_clients' => $actual_clients,
                            'score' => $score,
                        ];
                    }
                } else if ($request->group == 'products') {
                    $sales = Sale::where('disbursement_date', 'LIKE', "%$currentMonthYear%")->get()->groupBy('product_id');
                    $data = [];
                    foreach ($sales as $key => $sale) {
                        $branch_name = $sale->first()->branch->branch_name ?? "unkown";
                        $product_name = $sale->first()->product->product_name ?? 'unknown';
                        $target_amount = $sale->first()->product->productTarget->target_amount ?? 0;
                        $target_clients = 0;
                        $total_disbursement_amount = $sale->sum('disbursement_amount');
                        $actual_clients = $sale->sum('number_of_group_members');
                        $balance = $target_amount - $total_disbursement_amount;
                        if ($target_amount == 0) {
                            $percentage = 0;
                        } else {
                            $percentage = ($total_disbursement_amount / $target_amount) * 100;
                        }

                        $data[] = [
                            'branch_name' => $branch_name,
                            'product_name' => $product_name,
                            'total_disbursement_amount' => $total_disbursement_amount,
                            'target_amount' => $target_amount,
                            'balance' => $balance,
                            'target_clients' => $target_clients,
                            'actual_clients' => $actual_clients,
                            'score' => round($percentage, 0),
                        ];
                    }
                } else if ($request->group == 'officers') {
                    $sales = Sale::where('disbursement_date', 'LIKE', "%$currentMonthYear%")->get()->groupBy('staff_id');
                    $data = [];
                    foreach ($sales as $key => $sale) {
                        $staff_name = $sale->first()->officer->names;
                        $total_disbursement_amount = $sale->sum('disbursement_amount');
                        $number_of_clients = $sale->count();
                        $data[] = [
                            'staff_id' => $key,
                            'names' => $staff_name,
                            'total_disbursement_amount' => $total_disbursement_amount,
                            'number_of_clients' => $number_of_clients,
                        ];
                    }
                } else if ($request->group == 'regions-loans' || $request->group == 'regions-clients') {
                    $sales = Sale::where('disbursement_date', 'LIKE', "%$currentMonthYear%")->get()->groupBy('region_id');
                    $data = [];
                    foreach ($sales as $key => $sale) {
                        $region_name = $sale->first()->region->region_name;
                        $target_amount = $sale->first()->region->branches->sum('branchTarget.target_amount') ?? 0;
                        $target_clients = $sale->first()->region->branches->sum('branchTarget.target_numbers') ?? 0;
                        $total_disbursement_amount = $sale->sum('disbursement_amount');
                        $actual_clients = $sale->sum('number_of_group_members');
                        $balance_loans = $target_amount - $total_disbursement_amount;
                        $balance_clients = $target_clients - $actual_clients;
                        $balance = $request->group == 'regions-loans' ? $balance_loans : $balance_clients;
                        if ($target_amount == 0) {
                            $percentage = 0;
                        } else {
                            $percentage = ($total_disbursement_amount / $target_amount) * 100;
                        }

                        if ($target_clients == 0) {
                            $percentage_clients = 0;
                        } else {
                            $percentage_clients = ($actual_clients / $target_clients) * 100;
                        }

                        $score = $request->group == 'regions-loans' ? round($percentage, 0) : round($percentage_clients, 0);

                        $data[] = [
                            'region_id' => $sale->first()->region->region_id,
                            'region_name' => $region_name,
                            'total_disbursement_amount' => $total_disbursement_amount,
                            'target_amount' => $target_amount,
                            'balance' => $balance,
                            'target_clients' => $target_clients,
                            'actual_clients' => $actual_clients,
                            'score' => $score,
                        ];
                    }
                }
            } else {
                //sales categorized by branches
                $sales = Sale::where('disbursement_date', 'LIKE', "%$currentMonthYear%")->get()->groupBy('branch_id');
                //process the sales data and return the view
                $data = [];
                foreach ($sales as $key => $sale) {
                    //i want region_name, branch_name, and the total disbursement_amount
                    $region_name = $sale->first()->region->region_name;
                    $branch_name = $sale->first()->branch->branch_name;
                    //target_amount
                    $target_amount = $sale->first()->branch->branchTarget->target_amount ?? 0;
                    $total_disbursement_amount = $sale->sum('disbursement_amount');
                    //balance
                    $balance = $target_amount - $total_disbursement_amount;
                    //%centage score
                    if ($target_amount == 0) {
                        $percentage = 0;
                    } else {
                        $percentage = ($total_disbursement_amount / $target_amount) * 100;
                    }

                    $data[] = [
                        'region_name' => $region_name,
                        'branch_name' => $branch_name,
                        'total_disbursement_amount' => $total_disbursement_amount,
                        'target_amount' => $target_amount,
                        'balance' => $balance,
                        'score' => round($percentage, 0),
                    ];
                }
            }

            // Return JSON response with data and success message
            return response()->json(['data' => $data, 'message' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['error' => 'Failed to process request. Please try again.', 'exception' => $e->getMessage()], 500);
        }
    }
}
