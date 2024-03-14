<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    public $incrementing = false;

    protected $fillable = [
        'product_id',
        'product_name'
    ];

    //each product has a target amount
    public function productTarget()
    {
        return $this->hasOne(ProductTarget::class, 'product_id', 'product_id');
    }

    //each product belongs to 0 or more Arrears
    public function arrears()
    {
        return $this->hasMany(Arrear::class, 'product_id', 'product_id');
    }
}
