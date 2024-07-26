<?php

namespace App\Imports;

use App\Models\BranchTarget;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BranchTargetsImport implements ToModel, WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new BranchTarget([
            'branch_id' => $row[0],
            'target_amount' => $row[2],
            'target_numbers' => $row[3],
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
