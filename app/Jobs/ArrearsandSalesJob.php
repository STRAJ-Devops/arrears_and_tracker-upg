<?php

namespace App\Jobs;

use App\Models\Arrear;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\District;
use App\Models\Officer;
use App\Models\PreviousEndMonth;
use App\Models\Product;
use App\Models\Region;
use App\Models\Sale;
use App\Models\Sub_County;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ArrearsandSalesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        //and execution time to 30  minutes
        ini_set('max_execution_time', 1800);
        Log::info('Importing sales and arrears data from CSV file...');
        // Get the file path
        $file = storage_path('app/public/uploads/Arrears.csv');
        //iif the file does not exist, return an error
        if (!file_exists($file)) {
            //prnt an error in the cmd
            Log::error('File does not exist' . $file);
            //return an error
            return response()->json(['error' => 'File does not exist'], 404);
        } else {
            //file found
            Log::info('File found');
        }
        // Read the CSV file
        $csv = array_map('str_getcsv', file($file));

        // Truncate the sales and arrears tables
        Arrear::truncate();
        Sale::truncate();

        $handle = fopen($file, "r");
        $current_line = 0;

        Log::info("Starting". Carbon::now());
        while (($row = fgetcsv($handle)) !== FALSE) {
            if ($current_line > 0) {
                try {
                    //code...
                    $region = Region::firstOrCreate(['region_id' => trim(explode('-', $row[0])[0])], ['region_name' => trim(explode('-', $row[0])[1])]);
                    $branch = Branch::firstOrCreate(['branch_id' => trim(explode('-', $row[1])[0])], ['branch_name' => trim(explode('-', $row[1])[1]), 'region_id' => $region->region_id]);
                    $officer = Officer::firstOrCreate(['staff_id' => trim(explode('-', $row[2])[0])], ['names' => trim(explode('-', $row[2])[1]), 'username' => trim(explode(' ', explode('-', $row[2])[1])[0]), 'user_type' => 1, 'password' => bcrypt(trim(explode(' ', explode('-', $row[2])[1])[0])), 'un_hashed_password' => trim(explode(' ', explode('-', $row[2])[1])[0]), 'region_id' => $region->region_id, 'branch_id' => $branch->branch_id]);
                    $product = Product::firstOrCreate(['product_id' => trim($row[17])], ['product_name' => trim($row[18])]);
                    $district = District::firstOrCreate(['district_id' => trim(explode('-', $row[62])[0])], ['district_name' => trim(explode('-', $row[62])[1]), 'region_id' => $region->region_id]);
                    $subcounty = Sub_County::firstOrCreate(['subcounty_id' => trim(explode('-', $row[63])[0])], ['subcounty_name' => trim(explode('-', $row[63])[1]), 'district_id' => $district->district_id]);
                    $village = !empty($row[61]) ? Village::firstOrCreate(['village_name' => trim($row[61]), 'subcounty_id' => $subcounty->subcounty_id]) : Village::first();
                    $customer = Customer::firstOrCreate(
                        ['customer_id' => !empty($row[7]) ? trim($row[7]) : trim($row[12])],
                        [
                            'names' => !empty($row[8]) ? trim($row[8]) : 'Unknown',
                            'phone' => !empty($row[9]) ? trim($row[9]) : 'Unknown'
                        ]
                    );
                    $sale = Sale::create(
                        [
                            'number_of_children' => trim($row[45]),
                            'disbursement_amount' => trim($row[27]),
                            'gender' => trim($row[19]),
                            'disbursement_date' => trim($row[30]),
                            'number_of_group_members' => !empty($row[47]) ? trim($row[47]) : 0,
                            'number_of_women' => !empty($row[48]) ? trim($row[48]) : 0,
                            'group_id' => !empty($row[4]) ? trim($row[4]) : trim($row[12]),
                            'staff_id' => $officer->staff_id,
                            'product_id' => $product->product_id,
                            'region_id' => $region->region_id,
                            'branch_id' => $branch->branch_id
                        ]
                    );
                    $arrear = Arrear::create(
                        [
                            'customer_id' => !empty($row[7]) ? trim($row[7]) : trim($row[12]),
                            'group_id' => !empty($row[4]) ? trim($row[7]) : trim($row[12]),
                            'gender' => trim($row[19]),
                            'outsanding_principal' => !empty($row[35]) ? trim($row[35]) : 0,
                            'outstanding_interest' => !empty($row[40]) ? trim($row[40]) : 0,
                            'interest_in_arrears' => !empty($row[44]) ? trim($row[44]) : 0,
                            'real_outstanding_interest' => !empty($row[36]) ? trim($row[36]) : 0,
                            'next_repayment_date' => !empty(trim($row[32])) ? trim($row[32]) : 0,
                            'disbursement_date' => !empty(trim($row[30])) ? trim($row[30]) : 0,
                            'maturity_date' => !empty(trim($row[31])) ? trim($row[31]) : 0,
                            'principal_arrears' => trim($row[39]),
                            'number_of_days_late' => trim($row[41]),
                            'number_of_group_members' => !empty($row[47]) ? trim($row[47]) : 0,
                            'number_of_women' => !empty($row[48]) ? trim($row[48]) : 0,
                            'lending_type' => !empty($row[20]) ? trim($row[20]) : "Unknown",
                            'par' => trim($row[42]),
                            'amount_disbursed' => trim($row[27]),
                            'next_repayment_principal' => trim($row[33]),
                            'next_repayment_interest' => trim($row[34]),
                            'next_repayment_date' => trim($row[32]),
                            'disbursement_date' => trim($row[30]),
                            'draw_down_balance' => trim($row[44]),
                            'savings_balance' => trim($row[43]),
                            'group_name' => trim($row[3]),
                            'maturity_date' => trim($row[31]),
                            'staff_id' => $officer->staff_id,
                            'branch_id' => $branch->branch_id,
                            'region_id' => $region->region_id,
                            'product_id' => $product->product_id,
                            'district_id' => $district->district_id,
                            'subcounty_id' => $subcounty->subcounty_id,
                            'village_id' => $village->village_id,
                        ]
                    );
                    if (Carbon::now()->endOfMonth()->isToday()) {
                        $previousEndMonth = PreviousEndMonth::create(
                            [
                                'customer_id' => !empty($row[7]) ? trim($row[7]) : trim($row[12]),
                                'group_id' => !empty($row[4]) ? trim($row[7]) : trim($row[12]),
                                'gender' => trim($row[19]),
                                'outsanding_principal' => !empty($row[35]) ? trim($row[35]) : 0,
                                'outstanding_interest' => !empty($row[40]) ? trim($row[40]) : 0,
                                'interest_in_arrears' => !empty($row[44]) ? trim($row[44]) : 0,
                                'principal_arrears' => trim($row[39]),
                                'number_of_days_late' => trim($row[41]),
                                'number_of_group_members' => !empty($row[47]) ? trim($row[47]) : 0,
                                'lending_type' => !empty($row[20]) ? trim($row[20]) : "Unknown",
                                'par' => trim($row[42]),
                                'amount_disbursed' => trim($row[27]),
                                'next_repayment_principal' => trim($row[33]),
                                'next_repayment_interest' => trim($row[34]),
                                'next_repayment_date' => trim($row[32]),
                                'staff_id' => $officer->staff_id,
                                'branch_id' => $branch->branch_id,
                                'region_id' => $region->region_id,
                                'product_id' => $product->product_id,
                                'district_id' => $district->district_id,
                                'subcounty_id' => $subcounty->subcounty_id,
                                'village_id' => $village->village_id,
                            ]
                        );
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                    Log::error("Failed". $current_line);
                }
            }
            $current_line++;
        }
        fclose($handle);
        Log::info("Finish". Carbon::now());
    }
}
