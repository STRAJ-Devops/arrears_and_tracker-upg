<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Officer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SalesImport implements ToModel, WithStartRow, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Extracting region_id from $row[0]
        $regionData = explode('-', $row[0]);
        $region_id = $regionData[0];

        // Extracting product_id from $row[1]
        $branchData = explode('-', $row[1]);
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

        //extracting staff_id from $row[2]
        $staffData = explode('-', $row[2]);
        $staff_id = $staffData[0];

        $found = Officer::where('staff_id', $staff_id)->first();
        if (!$found) {
            //staffName is the rest of the string after the first hyphen
            $staffName = $staffData[1];
            $staff = new Officer();
            $staff->id = $staff_id;
            $staff->staff_id = $staff_id;
            $staff->names = $staffName;
            $staff->user_type = 1;
            $staff->username = $staff_id;
            $staff->password = Hash::make($staff_id);
            $staff->save();

            $staff_id = $staff->staff_id;
        }

        $product_id = $row[17];
        $found = Product::where('product_id', $product_id)->first();
        if (!$found) {
            $product = new Product();
            $product->product_id = $product_id;
            $product->product_name = $row[18];
            $product->save();

            $product_id = $product->product_id;
        }

        // Remove commas from disbursement_amount
        $disbursement_amount = str_replace(',', '', $row[27]);

        return new Sale([
            'staff_id' => $staff_id,
            'product_id' => $row[17],
            'disbursement_date' => $row[30],
            'disbursement_amount' => $disbursement_amount,
            'region_id' => $region_id,
            'branch_id' => $branch_id,
            'gender' => $row[19],
            'number_of_children' => $row[45],
        ]);
    }


    /**
     * @return int
     */
    public function startRow(): int
    {
        return 6;
    }

    public function chunkSize(): int
    {
        return 2000;
    }
}
