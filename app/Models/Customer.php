<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ["customer_id", "names", "phone"];

    //a customer has many arrears
    public function arrears()
    {
        return $this->hasMany(Arrear::class, "customer_id", "id");
    }

    //a customer has many comments
    public function comments()
    {
        return $this->hasMany(Comment::class, "customer_id", "customer_id");
    }

    //an arrear belongs to a customer
    
}
