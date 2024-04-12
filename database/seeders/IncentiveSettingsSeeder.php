<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncentiveSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('incentive_settings')->insert([
            'max_par' => 6.5,
            'percentage_incentive_par' => 20.0,
            'max_cap_portifolio' => 40000000,
            'min_cap_portifolio' => 5000000,
            'percentage_incentive_portifolio' => 40,
            'max_cap_client' => 20,
            'min_cap_client' => 5,
            'percentage_incentive_client' => 40,
            'max_incentive' => 500000,
        ]);
    }
}
