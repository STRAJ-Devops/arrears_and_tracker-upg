<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerTarget extends Model
{
    use HasFactory;

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'officer_id', 'staff_id');
    }
}
