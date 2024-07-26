<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WrittenOff extends Model
{
    use HasFactory;

    protected $fillable = [
        'officer_name',
        'contract_id',
        'customer_id',
        'csa',
        'dda',
        'write_off_date',
        'principal_written_off',
        'interest_written_off',
        'principal_paid',
        'interest_paid',
    ];
}
