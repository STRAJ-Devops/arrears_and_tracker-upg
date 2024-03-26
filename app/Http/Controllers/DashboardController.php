<?php

namespace App\Http\Controllers;

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
        $logged_user = auth()->user()->user_type;
        $staff_id = auth()->user()->staff_id;
        $outstanding_principal = $logged_user == 1 ? Arrear::sum('outsanding_principal') : Arrear::where('staff_id', $staff_id)->sum('outsanding_principal');

        $outstanding_interest = $logged_user == 1 ? Arrear::sum('outstanding_interest') : Arrear::where('staff_id', $staff_id)->sum('outstanding_interest');

        $principal_arrears = $logged_user == 1 ? Arrear::sum('principal_arrears') : Arrear::where('staff_id', $staff_id)->sum('principal_arrears');


        //get the sgl by counting number_of_group_members where product_code is 21070
        $sgl = $logged_user == 1 ? Arrear::where('product_id', 21070)->sum('number_of_group_members') : Arrear::where('staff_id', $staff_id)->where('product_id', 21070)->sum('number_of_group_members');

        $number_of_female_borrowers = $logged_user == 1 ? Sale::where('gender', 'female')->count() : Sale::where('gender', 'female')->where('staff_id', $staff_id)->count();

        $number_of_children = $logged_user == 1 ? Sale::sum('number_of_children') : Sale::where('staff_id', $staff_id)->sum('number_of_children');

        $currentMonthYear = date_create()->format('M-y'); // Get the current month abbreviation like "Mar"

        $total_disbursements_this_month = $logged_user == 1
        ? Sale::where('disbursement_date', 'LIKE', "%$currentMonthYear%")->sum('disbursement_amount')
        : Sale::where('staff_id', $staff_id)
            ->where('disbursement_date', 'LIKE', "%$currentMonthYear%")
            ->sum('disbursement_amount');
        $total_targets = BranchTarget::sum('target_amount');
        $number_of_clients = $logged_user == 1
        ? Sale::sum('number_of_group_members')
        : Sale::where('staff_id', $staff_id)
            ->sum('number_of_group_members');

        $number_of_groups = $logged_user == 1
        ? DB::table(DB::raw('(SELECT DISTINCT group_id FROM arrears WHERE lending_type = "Group") as distinct_groups'))
        ->count() : Arrear::where('lending_type', 'Group')->where('staff_id', $staff_id)->groupBy('group_id')
            ->count();

        $number_of_individuals = $logged_user == 1
        ? Arrear::where('lending_type', 'Group')->sum('number_of_group_members') : Arrear::where('lending_type', 'Group')->where('staff_id', $staff_id)->sum('number_of_group_members');


        //get par 30 days that is sum of par for all arrears that are more than 30 days late
        $par_30_days = $logged_user == 1 ? Arrear::where('number_of_days_late', '>', 30)->sum('par') : Arrear::where('staff_id', $staff_id)->where('number_of_days_late', '>', 30)->sum('par');

        $par_30_per = $outstanding_principal == 0 ? 0 : (($par_30_days / $outstanding_principal) * 100);

        //get pa 1 day that is sum of par for all arrears that are more than 1 day late
        $par_1_days = $logged_user == 1 ? Arrear::sum('par') : Arrear::where('staff_id', $staff_id)->sum('par');

        $par_1_per = $outstanding_principal == 0 ? 0 : (($par_1_days / $outstanding_principal) * 100);

        // Get product labels and targets
        $productData = Product::with('productTarget')->get();
        $brachData = Branch::with('branchTarget')->get();
        $productLabels = $productData->pluck('product_name', 'product_id')->toArray();
        $branchLabels = $brachData->pluck('branch_name', 'branch_id')->toArray();
        $productTargets = $productData->pluck('productTarget.target_amount', 'product_id')->toArray();
        $branchTargets = $brachData->pluck('branchTarget.target_amount', 'branch_id')->toArray();
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
            'outstanding_principal' => $outstanding_principal,
            'outstanding_interest' => $outstanding_interest,
            'principal_arrears' => $principal_arrears,
            'number_of_female_borrowers' => $number_of_female_borrowers,
            'number_of_children' => $number_of_children,
            'total_disbursements' => $total_disbursements_this_month,
            'par_30_days' => number_format(round($par_30_per, 2), 2),
            'par_1_days' => number_format(round($par_1_per, 2), 2),
            'number_of_clients' => $number_of_clients,
            'number_of_groups' => $number_of_groups,
            'number_of_individuals' => $number_of_individuals,
            'product_labels' => $labels,
            'product_targets' => $targets,
            'product_sales' => $sales,
            'branch_labels' => $branchLabelsList,
            'branch_targets' => $branchTargetsList,
            'branch_sales' => $branchSalesList,
            // 'withArrears' => $withArrears,
            // 'withOutArrears' => $withOuthArrears,
            'total_targets' => $total_targets,
            'sgl' => $sgl,
        ];

        return view('dashboard', compact('data'));
    }
}
