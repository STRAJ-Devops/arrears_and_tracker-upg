<?php

namespace App\Models;

use App\Models\Scopes\CommentScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy(CommentScope::class)]
class Comment extends Model
{
    use HasFactory;

    protected $fillable = ["staff_id", "customer_id", "comment", "number_of_days_late"];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

}
