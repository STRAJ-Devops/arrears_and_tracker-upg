<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'target_amount',
        'target_numbers',
    ];

    // Define the relationship with the Branch model
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }
}
