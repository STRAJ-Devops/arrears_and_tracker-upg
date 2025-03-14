<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $primaryKey = 'district_id';

    //fillables
    protected $fillable = [
        'district_id',
        'district_name',
        'region_id'
    ];
}
