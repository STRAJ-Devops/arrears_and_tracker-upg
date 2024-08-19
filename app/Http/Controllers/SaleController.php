<?php

namespace App\Http\Controllers;

use App\Jobs\ImportSalesAndArrearsJob;
use App\Models\Arrear;
use App\Models\Branch;
use App\Models\District;
use App\Models\Officer;
use App\Models\PreviousEndMonth;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Sub_County;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    public function index()
    {
        return view('tracker');
    }

    public function group_by(Request $request)
    {
        $currentMonthYear = DB::table('upload_date')->latest()->value('upload_date') ?? date('M-y');
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
                } else if ($request->group == 'officers-loans' || $request->group == 'officers-clients') {
                    $sales = Sale::where('disbursement_date', 'LIKE', "%$currentMonthYear%")->get()->groupBy('staff_id');
                    $data = [];
                    foreach ($sales as $key => $sale) {
                        $staff_name = $sale->first()->officer->names;
                        $total_disbursement_amount = $sale->sum('disbursement_amount');
                        $number_of_clients = $sale->sum('number_of_group_members');
                        //get officer target amount and number of clients
                        $target_amount = $sale->first()->officer->officerTarget->target_amount ?? 0;
                        $target_clients = $sale->first()->officer->officerTarget->target_numbers ?? 0;

                        //balance
                        $balance_loans = $target_amount - $total_disbursement_amount;

                        //balance clients
                        $balance_clients = $target_clients - $number_of_clients;

                        //actual balance
                        $balance = $request->group == 'officers-loans' ? $balance_loans : $balance_clients;

                        //%centage score for loans
                        if ($target_amount <= 0) {
                            $percentage = 0;
                        } else {
                            $percentage = ($total_disbursement_amount / $target_amount) * 100;
                        }

                        //%centage score for clients
                        if ($target_clients == 0) {
                            $percentage_clients = 0;
                        } else {
                            $percentage_clients = ($number_of_clients / $target_clients) * 100;
                        }
                        $data[] = [
                            'staff_id' => $key,
                            'names' => $staff_name,
                            'total_disbursement_amount' => $total_disbursement_amount,
                            'number_of_clients' => $number_of_clients,
                            'target_amount' => $target_amount,
                            'balance' => $balance,
                            'target_clients' => $target_clients,
                            'actual_clients' => $number_of_clients,
                            'score' => $request->group == 'officers-loans' ? round($percentage, 0) : round($percentage_clients, 0),
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

    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'upload_template_file' => 'required',
        ], [
            'upload_template_file.required' => 'Please upload a file.',
            'upload_template_file.mimes' => 'The uploaded file must be a valid CSV file.',
        ]);

        // Save the file to the server
        $file = $request->file('upload_template_file');
        $file_name = time() . '_' . $file->getClientOriginalName();
        $save = $file->move(public_path('uploads'), $file_name);

        // Check if the file was successfully saved
        if (!$save) {
            return response()->json(['error' => 'Failed to save file. Please try again.'], 400);
        }

        // Dispatch the job
        ImportSalesAndArrearsJob::dispatch($file_name, auth()->user()->staff_id);

        // Return a success message
        return response()->json(['message' => 'Sales and arrears import started successfully.'], 200);
    }


    public function isLastDayOfMonth()
    {
        $today = Carbon::now();
        return $today->isLastOfMonth();
    }

    public function importPreviousEndMonthSales(Request $request)
    {
        ini_set('max_execution_time', 1200);
        ini_set('memory_limit', '-1');
        // Validate the uploaded file
        $request->validate([
            'upload_template_file' => 'required|mimes:xlsx,xls,csv',
        ], [
            'upload_template_file.required' => 'Please upload a file.',
            'upload_template_file.mimes' => 'The uploaded file must be a valid Excel or CSV file.',
        ]);

        //save the file to the server
        $file = $request->file('upload_template_file');
        $file_name = time() . '_' . $file->getClientOriginalName();
        $save = $file->move(public_path('uploads'), $file_name);

        // Check if the file was successfully saved
        if (!$save) {
            return response()->json(['error' => 'Failed to save file. Please try again.'], 400);
        } else {
            // Check if the file is a CSV
            if ($file->getClientOriginalExtension() == 'csv') {
                //read the csv file
                $file = public_path('uploads/' . $file_name);
                $csv = array_map('str_getcsv', file($file));

                //truncate the sales and arrears table

                PreviousEndMonth::truncate();

                for ($i = 5; $i < count($csv); $i++) {
                    try {
                        // Extracting region_id from $csv[$i][0]
                        $regionData = explode('-', $csv[$i][0]);
                        $region_id = $regionData[0];

                        // Extracting product_id from $csv[$i][1]
                        $branchData = explode('-', $csv[$i][1]);
                        $branch_id = $branchData[0];
                        $found = Branch::where('branch_id', $branch_id)->first();
                        if (!$found) {
                            $branch = new Branch();
                            $branch->branch_id = $branch_id;
                            $branch->branch_name = $branchData[1];
                            $branch->region_id = $region_id;
                            $branch->save();

                            $branch_id = $branch->branch_id;
                        }

                        //extracting staff_id from $csv[$i][2]
                        $staffData = explode('-', $csv[$i][2]);
                        $staff_id = $staffData[0];
                        $full_name = count($staffData) > 2 ? $staffData[2] : $staffData[1];

                        $found = Officer::where('staff_id', $staff_id)->first();
                        if ($found) {
                            // Staff found, check if names match
                            if ($found->names !== $full_name) {
                                $password = bcrypt($staff_id);
                                // Names don't match, update name
                                $found->names = $full_name;
                                $found->user_type = 1;
                                $found->username = $staff_id;
                                $found->region_id = $region_id;
                                $found->branch_id = $branch_id;
                                $found->password = $password;
                                $found->un_hashed_password = $staff_id;
                                $found->save();
                            }
                        } else {
                            $password = bcrypt($staff_id);

                            // Staff not found, create new
                            $staff = new Officer();

                            $staff->staff_id = $staff_id;
                            $staff->names = $full_name;
                            $staff->user_type = 1;
                            $staff->username = $staff_id;
                            $staff->region_id = $region_id;
                            $staff->branch_id = $branch_id;
                            $staff->password = $password;
                            $staff->un_hashed_password = $staff_id;
                            $staff->save();

                        }

                        $product_id = $csv[$i][17];
                        $found = Product::where('product_id', $product_id)->first();
                        if (!$found) {
                            $product = new Product();
                            $product->product_id = $product_id;
                            $product->product_name = $csv[$i][18];
                            $product->save();

                            $product_id = $product->product_id;
                        }

                        $district_id = explode('-', $csv[$i][62])[0];
                        $district = District::firstOrCreate(
                            ['district_id' => $district_id],
                            [
                                'district_name' => "Unknown",
                                'region_id' => $region_id,
                            ]
                        );

                        $subcounty_id = explode('-', $csv[$i][63])[0];
                        $subcounty = Sub_County::firstOrCreate(
                            ['subcounty_id' => $subcounty_id],
                            [
                                'subcounty_name' => "Unknown",
                                'district_id' => $district_id,
                            ]
                        );

                        $village_id = null;
                        if (!empty($csv[$i][61])) {
                            $village = \App\Models\Village::firstOrCreate(
                                ['village_name' => $csv[$i][61]],
                                ['subcounty_id' => $subcounty_id]
                            );
                            $village_id = $village->village_id;
                        } else {
                            $village = \App\Models\Village::create([
                                'village_name' => 'Unknown',
                                'subcounty_id' => $subcounty_id,
                            ]);
                            $village_id = $village->village_id;
                        }

                        $csv[$i][47] = $csv[$i][47] == "" ? 1 : $csv[$i][47];

                        [$csv[$i][27], $csv[$i][35], $csv[$i][40], $csv[$i][39], $csv[$i][42], $csv[$i][44], $csv[$i][33], $csv[$i][34]] = array_map(function ($value) {
                            return str_replace(',', '', $value);
                        }, [$csv[$i][27], $csv[$i][35], $csv[$i][40], $csv[$i][39], $csv[$i][42], $csv[$i][44], $csv[$i][33], $csv[$i][34]]);

                        $customer_id_column = $csv[$i][7] == '' ? $csv[$i][12] : $csv[$i][7];
                        //get the customer data and save it to get the customer_id
                        //check if the customer exists by checking $csv[$i][7] or $csv[$i][12]
                        $customer = \App\Models\Customer::where('customer_id', $customer_id_column)->first();
                        //if the customer does not exist, create a new customer
                        if (!$customer) {
                            $customer = new \App\Models\Customer();
                            $customer->customer_id = $customer_id_column;
                            $customer->names = $csv[$i][8] ?? "Unknown";
                            $customer->phone = $csv[$i][9] ?? 'Unknown';
                            //save the customer
                            $customer->save();
                        }

                        $previous_end_month = new PreviousEndMonth();
                        $previous_end_month->staff_id = $staff_id;
                        $previous_end_month->branch_id = $branch_id;
                        $previous_end_month->region_id = $region_id;
                        $previous_end_month->product_id = $product_id;
                        $previous_end_month->district_id = $district_id;
                        $previous_end_month->subcounty_id = $subcounty_id;
                        $previous_end_month->village_id = $village_id;
                        $previous_end_month->outsanding_principal = $csv[$i][35];
                        //this is is the interest in arrears
                        $previous_end_month->outstanding_interest = $csv[$i][40];
                        //this is add column to the arrears table
                        $previous_end_month->interest_in_arrears = $csv[$i][44] ?? 0;
                        $previous_end_month->principal_arrears = $csv[$i][39];
                        $previous_end_month->number_of_days_late = $csv[$i][41];
                        $previous_end_month->number_of_group_members = $csv[$i][47];
                        $previous_end_month->lending_type = $csv[$i][20] ?? 'Unknown';
                        $previous_end_month->par = $csv[$i][42];
                        $previous_end_month->gender = $csv[$i][19] ?? 'Unknown';
                        $previous_end_month->customer_id = $customer->customer_id;
                        $previous_end_month->amount_disbursed = $csv[$i][27];
                        $previous_end_month->next_repayment_principal = $csv[$i][33];
                        $previous_end_month->next_repayment_interest = $csv[$i][34];
                        $previous_end_month->next_repayment_date = $csv[$i][32];
                        $previous_end_month->group_id = blank($csv[$i][7]) ? $csv[$i][12] : $csv[$i][7];

                        $previous_end_month->save();
                    } catch (\Exception $e) {
                        return response()->json(['error' => 'Failed to process CSV. Please ensure the file format is correct.', 'exception' => $e->getMessage()], 400);
                    }
                }
            }
        }
        // Return a success message upon successful import
        return response()->json(['message' => 'Sales and arrears imported successfully.'], 200);
    }

    public function process_csv_for_arrears($file_name)
    {
        ini_set('max_execution_time', 1200);
        ini_set('memory_limit', '-1');
        //get column names from the csv
        $file = public_path('uploads/' . $file_name);
        $csv = array_map('str_getcsv', file($file));

        //set excution time to 5 minutes
        for ($i = 5; $i < count($csv); $i++) {
            try {
                // Extracting region_id from $csv[$i][0]
                $regionData = explode('-', $csv[$i][0]);
                $region_id = $regionData[0];

                // Extracting product_id from $csv[$i][1]
                $branchData = explode('-', $csv[$i][1]);
                $branch_id = $branchData[0];
                $found = Branch::where('branch_id', $branch_id)->first();
                if (!$found) {
                    $branch = new Branch();
                    $branch->branch_id = $branch_id;
                    $branch->branch_name = $branchData[1];
                    $branch->region_id = $region_id;
                    $branch->save();

                    $branch_id = $branch->branch_id;
                }

                //extracting staff_id from $csv[$i][2]
                $staffData = explode('-', $csv[$i][2]);
                $staff_id = $staffData[0];

                $found = Officer::where('staff_id', $staff_id)->first();
                if (!$found) {
                    //staffName is the rest of the string after the first hyphen
                    $staffName = count($staffData) > 2 ? $staffData[2] : $staffData[1];
                    $staff = new Officer();
                    $staff->staff_id = $staff_id;
                    $staff->names = $staffName;
                    $staff->user_type = 1;
                    $staff->username = $staff_id;
                    $staff->password = bcrypt($staff_id);
                    $staff->un_hashed_password = $staff_id;
                    $staff->save();

                    $staff_id = $staff->staff_id;
                }

                $product_id = $csv[$i][17];
                $found = Product::where('product_id', $product_id)->first();
                if (!$found) {
                    $product = new Product();
                    $product->product_id = $product_id;
                    $product->product_name = $csv[$i][18];
                    $product->save();

                    $product_id = $product->product_id;
                }

                $district_id = explode('-', $csv[$i][62])[0];
                $district = District::firstOrCreate(
                    ['district_id' => $district_id],
                    [
                        'district_name' => "Unknown",
                        'region_id' => $region_id,
                    ]
                );

                $subcounty_id = explode('-', $csv[$i][63])[0];
                $subcounty = Sub_County::firstOrCreate(
                    ['subcounty_id' => $subcounty_id],
                    [
                        'subcounty_name' => "Unknown",
                        'district_id' => $district_id,
                    ]
                );

                $village_id = null;
                if (!empty($csv[$i][61])) {
                    $village = \App\Models\Village::firstOrCreate(
                        ['village_name' => $csv[$i][61]],
                        ['subcounty_id' => $subcounty_id]
                    );
                    $village_id = $village->village_id;
                } else {
                    $village = \App\Models\Village::create([
                        'village_name' => 'Unknown',
                        'subcounty_id' => $subcounty_id,
                    ]);
                    $village_id = $village->village_id;
                }

                $csv[$i][47] = $csv[$i][47] == "" ? 1 : $csv[$i][47];

                [$csv[$i][27], $csv[$i][35], $csv[$i][40], $csv[$i][39], $csv[$i][42]] = array_map(function ($value) {
                    return str_replace(',', '', $value);
                }, [$csv[$i][27], $csv[$i][35], $csv[$i][40], $csv[$i][39], $csv[$i][42]]);

                //get the customer data and save it to get the customer_id
                $customer = new \App\Models\Customer();
                $customer->customerId = $csv[$i][7];
                $customer->names = $csv[$i][8];
                $customer->phone = $csv[$i][9];

                //save the customer
                $customer->save();
                //insert a sale record
                $sale = new Sale();
                $sale->staff_id = $staff_id;
                $sale->product_id = $product_id;
                $sale->disbursement_date = $csv[$i][30];
                $sale->disbursement_amount = $csv[$i][27];
                $sale->region_id = $region_id;
                $sale->branch_id = $branch_id;
                $sale->gender = $csv[$i][19];
                $sale->number_of_children = $csv[$i][45];
                $sale->save();

                //insert a arrear record
                $arrear = new Arrear();
                $arrear->staff_id = $staff_id;
                $arrear->branch_id = $branch_id;
                $arrear->region_id = $region_id;
                $arrear->product_id = $product_id;
                $arrear->district_id = $district_id;
                $arrear->subcounty_id = $subcounty_id;
                $arrear->village_id = $village_id;
                $arrear->outsanding_principal = $csv[$i][35];
                $arrear->outstanding_interest = $csv[$i][40];
                $arrear->principal_arrears = $csv[$i][39];
                $arrear->number_of_days_late = $csv[$i][41];
                $arrear->number_of_group_members = $csv[$i][47];
                $arrear->lending_type = $csv[$i][20] ?? 'Unknown';
                $arrear->par = $csv[$i][42];
                $arrear->gender = $csv[$i][19] ?? 'Unknown';
                $arrear->customer_id = $customer->id;
                $arrear->amount_disbursed = $csv[$i][27];

                $arrear->save();

            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to process CSV. Please ensure the file format is correct.', 'exception' => $e->getMessage()], 400);
            }
        }
        return response()->json(['message' => 'Records imported successfully.'], 200);
    }

    public function fileUpload()
    {
        //validate the file to be :html
        request()->validate([
            'file' => 'required|mimes:html',
        ]);

        //save the file to public in uploads folder
        $file = request()->file('file');

        $file->move(public_path('uploads'), $file->getClientOriginalName());

        //resturn a success message with response 200
        return response()->json(['message' => 'File uploaded successfully.'], 200);
    }

    /**
     * truncate arrears and sales
     */

    public function truncateArrearsAndSales()
    {
        try {
            Sale::truncate();
            Arrear::truncate();
            return back()->with('success', 'Arrears and sales truncated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to truncate arrears and sales. Please try again.');
        }

    }

    public function truncatePreviousEndMonth()
    {
        try {
            PreviousEndMonth::truncate();
            return back()->with('success', 'Previous end month records truncated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to truncate previous end month records. Please try again.');
        }
    }

}
