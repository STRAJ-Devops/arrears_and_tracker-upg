<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ["customerId", "names", "phone"];

    //a customer has many arrears
    public function arrears()
    {
        return $this->hasMany(Arrear::class, "customer_id", "id");
    }
}
