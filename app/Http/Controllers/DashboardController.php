<?php

namespace App\Http\Controllers;

use App\Models\Arrear;
use App\Models\Branch;
use App\Models\BranchTarget;
use App\Models\DashboardCache;
use App\Models\OfficerTarget;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $request = Http::timeout(90)->get('https://test.ug.vft24.org/crmapi/v1/dashboard/data');
            $request_successful = $request->successful();
        } catch (\Throwable $th) {
            $request_successful = false;
        }
        
        //fetch data on request
        if ($request_successful) {
            $data = $request->json(['data']);
            DashboardCache::create(['data' => $data]);
        } else {
            $data = DashboardCache::latest()->value('data');
        }

        //get the user type and staff id
        $logged_user = auth()->user()->user_type;
        $staff_id = auth()->user()->staff_id;

        $outstanding_principal = (int) $data['outstandingPrincipal'];

        $outstanding_interest = (int) $data['interestArrears'];

        $principal_arrears = (int) $data['principalArrears'];

        //get the sgl by counting number_of_group_members where product_code is 21070
        $sgl = Arrear::where('product_id', 21070)->sum('number_of_group_members');
        //add AW column
        $number_of_female_borrowers =(int) $data['noOfWomen'];

        $number_of_children = (int) $data['noOfChildren'];

        // Get the current month abbreviation like "Mar-24"
        $currentMonthYear = DB::table('upload_date')->latest()->value('upload_date')??date('M-y');
        $total_disbursements_this_month = $data['newLoans'] ?? Sale::where('disbursement_date', 'LIKE', "%$currentMonthYear%")->sum('disbursement_amount');
        $number_of_clients = (int) $data['noOfClients'];

        $number_of_groups = (int) $data['noOfSolidarityGroup'];
        $number_of_individuals = (int) $data['noOfSolidarityMembers'];

        //get par 30 days that is sum of par for all arrears that are more than 30 days late
        $par_30_days = Arrear::where('number_of_days_late', '>', 30)->sum('par') ?? 0;

        $par_30_per = ($outstanding_principal == 0) ? 0 : (($par_30_days / $outstanding_principal) * 100);

        //get pa 1 day that is sum of par for all arrears that are more than 1 day late
        $par_1_days = Arrear::sum('par') ?? 0;

        $par_1_per = ($outstanding_principal == 0) ? 0 : (($par_1_days / $outstanding_principal) * 100);

        //get portifolio performance percentage
        //get officer target amount
        $officer_target = OfficerTarget::where('officer_id', $staff_id)->latest()->value('target_amount')??0;
        $officer_actual = Sale::where('disbursement_date', 'LIKE', "%$currentMonthYear%")->where('staff_id', $staff_id)->sum('disbursement_amount');
        $officer_performance = $officer_target == 0 ? 0 : (($officer_actual / $officer_target) * 100);

        //get client performance percentage
        //get number of clients target
        $clients_target = OfficerTarget::where('officer_id', $staff_id)->latest()->value('target_numbers')??0;
        $clients_actual = Sale::where('disbursement_date', 'LIKE', "%$currentMonthYear%")->where('staff_id', $staff_id)->distinct()->get(['group_id', 'number_of_group_members'])->sum('number_of_group_members');
        $clients_performance = $clients_target == 0 ? 0 : (($clients_actual / $clients_target) * 100);
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
            'outstanding_principal' => $outstanding_principal,
            'outstanding_interest' => $outstanding_interest,
            'principal_arrears' => $principal_arrears,
            'number_of_female_borrowers' => $number_of_female_borrowers,
            'number_of_children' => $number_of_children,
            'total_disbursements' => $total_disbursements_this_month,
            'total_targets' => $total_targets,
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
            'sgl' => $sgl,
            'officer_performance' => number_format($officer_performance),
            'clients_performance' => number_format($clients_performance),
        ];

        return view('dashboard', compact('data'));
    }
}
