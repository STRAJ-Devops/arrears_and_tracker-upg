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

        // NEW MSE FIELDS
        'max_cap_portifolio_mse',
        'min_cap_portifolio_mse',
        'percentage_incentive_portifolio_mse',
        'min_cap_client_mse',
        'max_par_mse',
        'max_llr_mse',
        'min_cap_client_growth_mse',
        'max_cap_client_growth_mse',
        'percentage_incentive_client_growth_mse',
        'max_incentive_mse',


        // Add to $fillable:
        'retention_weight_individual',
        'retention_weight_group',
        'retention_weight_sgl',
        'retention_weight_mse',
        'retention_min_individual',
        'retention_min_group',
        'retention_min_sgl',
        'retention_min_mse',
        'retention_max_individual',
        'retention_max_group',
        'retention_max_sgl',
        'retention_max_mse',

        'max_cap_portifolio_sme',
        'min_cap_portifolio_sme',


        'min_net_portfolio_growth_indv',
        'max_net_portfolio_growth_indv',
        'net_portfolio_growth_weight_indv',

        'percentage_incentive_par_individual',
        'percentage_incentive_par_mse',


    ];

    protected $casts = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Dynamically cast all fillable fields to double
        foreach ($this->fillable as $field) {
            $this->casts[$field] = 'double';
        }
    }
}
