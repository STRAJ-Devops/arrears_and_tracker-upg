<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreviousEndMonth extends Model
{
    use HasFactory;

    //table name
    protected $table = 'previous_end_month';

    protected $fillable = [
        'staff_id',
        'branch_id',
        'region_id',
        'product_id',
        'district_id',
        'subcounty_id',
        'village_id',
        'outsanding_principal',
        'outstanding_interest',
        'principal_arrears',
        'number_of_days_late',
        'number_of_group_members',
        'lending_type',
        'interest_in_arrears',
        'par',
        'gender',
        'customer_id',
        'amount_disbursed',
        'next_repayment_principal',
        'next_repayment_interest',
        'next_repayment_date',
        'dda',
        'group_id',
    ];
}
