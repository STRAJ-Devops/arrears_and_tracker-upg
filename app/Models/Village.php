<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $primaryKey = 'village_id';

    //fillables
    protected $fillable = [
        'village_id',
        'village_name',
        'subcounty_id'
    ];
}
