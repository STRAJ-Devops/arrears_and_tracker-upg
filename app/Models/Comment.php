<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ["staff_id", "arrear_id", "comment"];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'staff_id', 'id');
    }

}
