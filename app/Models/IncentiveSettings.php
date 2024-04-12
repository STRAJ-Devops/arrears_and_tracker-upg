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
    ];
}
