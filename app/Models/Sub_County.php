<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sub_County extends Model
{
    use HasFactory;

    protected $primaryKey = 'subcounty_id';

    //fillables
    protected $fillable = [
        'subcounty_id',
        'subcounty_name',
        'district_id'
    ];
}
