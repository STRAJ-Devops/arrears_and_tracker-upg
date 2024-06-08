<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncentiveSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'max_par',
        'percentage_incentive_par',
        'max_cap_portifolio',
        'min_cap_portifolio',
        'percentage_incentive_portifolio',
        'max_cap_client',
        'min_cap_client',
        'percentage_incentive_client',
        'max_incentive',
        'max_cap_portifolio_individual',
        'max_cap_portifolio_group',
        'max_cap_portifolio_fast',
        'min_cap_portifolio_fast',
        'min_cap_client_individual',
        'min_cap_client_group',
        'max_par_individual',
        'max_par_group',
        'max_par_fast',
        'max_llr_group',
        'max_llr_individual',
        'max_llr_fast',
        'max_cap_number_of_groups_fast',
        'min_cap_number_of_groups_fast',
    ];
}
