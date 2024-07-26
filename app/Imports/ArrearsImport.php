<?php

namespace App\Imports;

use App\Models\Arrear;
use App\Models\District;
use App\Models\Officer;
use App\Models\Product;
use App\Models\Sub_County;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ArrearsImport implements ToModel, WithStartRow, WithChunkReading
{
    public function model(array $row)
    {
        [$region_id, $region_name] = explode('-', $row[0]);
        [$staff_id, $staffName] = explode('-', $row[2]);
        [$branch_id, $branch_name] = explode('-', $row[1]);

        $found = Officer::firstOrCreate(
            ['staff_id' => $staff_id],
            [
                'id' => $staff_id,
                'names' => $staffName,
                'user_type' => 1,
                'username' => $staff_id,
                'password' => Hash::make($staff_id),
            ]
        );

        $found = \App\Models\Branch::firstOrCreate(
            ['branch_id' => $branch_id],
            [
                'branch_name' => $branch_name,
                'region_id' => $region_id,
            ]
        );

        $district_id = explode('-', $row[62])[0];
        $district = District::firstOrCreate(
            ['district_id' => $district_id],
            [
                'district_name' => "Unknown",
                'region_id' => $region_id,
            ]
        );

        $subcounty_id = explode('-', $row[63])[0];
        $subcounty = Sub_County::firstOrCreate(
            ['subcounty_id' => $subcounty_id],
            [
                'subcounty_name' => "Unknown",
                'district_id' => $district_id,
            ]
        );

        $village_id = null;
        if (!empty($row[64])) {
            $village = \App\Models\Village::firstOrCreate(
                ['village_name' => $row[64]],
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

        $product_id = $row[17];
        $product = Product::firstOrCreate(
            ['product_id' => $product_id],
            ['product_name' => $row[18]]
        );

        [$row[35], $row[36], $row[39], $row[42]] = array_map(function ($value) {
            return str_replace(',', '', $value);
        }, [$row[35], $row[36], $row[39], $row[42]]);

        return new Arrear([
            'staff_id' => $staff_id,
            'branch_id' => $branch_id,
            'region_id' => $region_id,
            'product_id' => $product_id,
            'district_id' => $district_id,
            'subcounty_id' => $subcounty_id,
            'village_id' => $village_id,
            'outsanding_principal' => $row[35],
            'outstanding_interest' => $row[36],
            'principal_arrears' => $row[39],
            'number_of_days_late' => $row[41],
            'number_of_group_members' => $row[47] ?? 1,
            'lending_type' => $row[20]??'Unknown',
            'par' => $row[42],
            'gender' => $row[19]??'Unknown',
        ]);
    }

    public function startRow(): int
    {
        return 6;
    }

    public function chunkSize(): int
    {
        return 2000;
    }
}
