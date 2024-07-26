<?php

namespace Database\Seeders;

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
            'max_cap_portifolio_individual' => 130000000,
            'max_cap_portifolio_group' => 90000000,
            'min_cap_client_individual' => 130,
            'min_cap_client_group' => 140,
            'max_par_individual' => 6.5,
            'max_par_group' => 6.5,
            'max_par_fast' => 6.5,
            'max_llr_group' => 0.18,
            'max_llr_individual' => 0.18,
            'max_llr_fast' => 0.18,
            "max_cap_number_of_groups_fast" => 60,
            "min_cap_number_of_groups_fast" => 30,
            'max_cap_portifolio_fast' => 20000000,
            'min_cap_portifolio_fast' => 5000000,
        ]);
    }
}
