<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $primaryKey = 'region_id';

    public $incrementing = false;

    protected $fillable = [
        'region_id',
        'region_name',
    ];

    //one region can have many branches
    public function branches()
    {
        return $this->hasMany(Branch::class, 'region_id', 'region_id');
    }

    //one region can have many sales
    public function sales()
    {
        return $this->hasMany(Sale::class, 'region_id', 'region_id');
    }

    //total amount of loans disbursed in a region
    public function totalDisbursement()
    {
        return $this->sales->sum('disbursement_amount');
    }
    //get regionTarget ad the sum of branch target amount in a region
    public function targetNumbers()
    {
        return $this->branches->sum('branchTarget.target_numbers');
    }

}
