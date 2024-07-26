<?php

namespace App\Imports;

use App\Models\ProductTarget;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductTargetsImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ProductTarget([
            'product_id' => $row[0],
            'target_amount' => $row[2],
        ]);
    }

        /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
}
