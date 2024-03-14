<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $primaryKey = 'branch_id';

    public $incrementing = false;

    protected $fillable = [
        'branch_id',
        'region_id',
        'branch_name',
    ];

    //one branch can have one region
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'region_id');
    }

    //each branch has a target
    public function branchTarget()
    {
        return $this->hasOne(BranchTarget::class, 'branch_id', 'branch_id');
    }
}
