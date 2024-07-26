<?php

namespace Database\Seeders;

use App\Models\BranchTarget;
use Illuminate\Database\Seeder;

class BranchTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create targets for all branches in the database
        $branches = \App\Models\Branch::all();
        foreach ($branches as $branch) {
            BranchTarget::factory()->create(['branch_id' => $branch->branch_id]);
        }
    }
}
