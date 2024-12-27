<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Arrear;
use App\Models\Branch;
use App\Models\BranchTarget;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $outstanding_principal = Arrear::sum('outsanding_principal');

        $outstanding_interest = Arrear::sum('outstanding_interest');

        $principal_arrears = Arrear::sum('principal_arrears');

        //get the sgl by counting number_of_group_members where product_code is 21070
        $sgl = Arrear::where('product_id', 21070)->sum('number_of_group_members');

        $number_of_female_borrowers = Sale::where('gender', 'female')->count() + Sale::where('product_id', 21070)->sum('number_of_women');

        $number_of_children = Sale::sum('number_of_children');

        // Get the current month abbreviation like "Mar-24"
        $currentMonthYear = DB::table('upload_date')->latest()->value('upload_date')??date('M-y');

        $total_disbursements_this_month = Sale::where('disbursement_date', 'LIKE', "%$currentMonthYear%")->sum('disbursement_amount');

        $number_of_clients = Sale::distinct()->get(['group_id', 'number_of_group_members'])->sum('number_of_group_members');

        $number_of_groups = Arrear::where('lending_type', 'Group')->distinct()->get(['group_id'])->count();

        $number_of_individuals = Arrear::where('lending_type', 'Group')->count();

        //get par 30 days that is sum of par for all arrears that are more than 30 days late
        $par_30_days = Arrear::where('number_of_days_late', '>', 30)->sum('par');

        $par_30_per = $outstanding_principal == 0 ? 0 : (($par_30_days / $outstanding_principal) * 100);

        //get pa 1 day that is sum of par for all arrears that are more than 1 day late
        $par_1_days = Arrear::sum('par');

        $par_1_per = $outstanding_principal == 0 ? 0 : (($par_1_days / $outstanding_principal) * 100);

        // Get product labels and targets
        $productData = Product::with('productTarget')->get();
        $brachData = Branch::with('branchTarget')->get();
        $productLabels = $productData->pluck('product_name', 'product_id')->toArray();
        $branchLabels = $brachData->pluck('branch_name', 'branch_id')->toArray();
        $productTargets = $productData->pluck('productTarget.target_amount', 'product_id')->toArray();
        $branchTargets = $brachData->pluck('branchTarget.target_amount', 'branch_id')->toArray();
        //get total targets by summing branch targets using eloquent
        $total_targets = BranchTarget::sum('target_amount');
        // Get product actuals for this month
        $productActuals = Sale::where('disbursement_date', 'LIKE', "%$currentMonthYear%")
            ->selectRaw('product_id, sum(disbursement_amount) as total_disbursements')
            ->groupBy('product_id')
            ->pluck('total_disbursements', 'product_id')
            ->toArray();
        $branchActuals = Sale::where('disbursement_date', 'LIKE', "%$currentMonthYear%")
            ->selectRaw('branch_id, sum(disbursement_amount) as total_disbursements')
            ->groupBy('branch_id')
            ->pluck('total_disbursements', 'branch_id')
            ->toArray();

        // Initialize arrays to store labels, targets, and sales
        $labels = [];
        $targets = [];
        $sales = [];

        // Loop through product labels and align targets and sales data
        foreach ($productLabels as $productId => $productName) {
            $labels[] = $productName;
            $targets[] = $productTargets[$productId] ?? 0; // Use null coalescing operator
            $sales[] = $productActuals[$productId] ?? 0; // Use null coalescing operator
        }

        $branchLabelsList = [];
        $branchTargetsList = [];
        $branchSalesList = [];

        // Loop through branch labels and align targets and sales data
        foreach ($branchLabels as $branchId => $branchName) {
            $branchLabelsList[] = $branchName;
            $branchTargetsList[] = $branchTargets[$branchId] ?? 0; // Use null coalescing operator
            $branchSalesList[] = $branchActuals[$branchId] ?? 0; // Use null coalescing operator
        }


        // Now you have aligned arrays $labels, $targets, and $sales where each index corresponds to the same product.

        $data = [
            'outstanding_principal' => (string)$outstanding_principal,
            'outstanding_interest' => (string)$outstanding_interest,
            'principal_arrears' => (string)$principal_arrears,
            'number_of_female_borrowers' => (string)$number_of_female_borrowers,
            'number_of_children' => (string)$number_of_children,
            'total_disbursements' => (string)$total_disbursements_this_month,
            'total_targets' => (string)$total_targets,
            'par_30_days' => (string)number_format(round($par_30_per, 2), 2),
            'par_1_days' => (string)number_format(round($par_1_per, 2), 2),
            'number_of_clients' => (string)$number_of_clients,
            'number_of_groups' => (string)$number_of_groups,
            'number_of_individuals' => (string)$number_of_individuals,
            'product_labels' => $labels,
            'product_targets' => $targets,
            'product_sales' => $sales,
            'branch_labels' => $branchLabelsList,
            'branch_targets' => $branchTargetsList,
            'branch_sales' => $branchSalesList,
            'sgl' => (string)$sgl,
        ];

        return response()->json(['data' => $data, 'message' => 'success'], 200);
    }
}
