<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EllGreen\LaravelLoadFile\Laravel\Traits\LoadsFiles;


class Sale extends Model
{
    use HasFactory, LoadsFiles;

    protected $fillable = [
        'staff_id',
        'product_id',
        'disbursement_date',
        'disbursement_amount',
        'region_id',
        'branch_id',
        'gender',
        'number_of_children',
    ];

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'staff_id', 'staff_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'region_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

    //get the total disbursed amount in the current month
    public function totalDisbursedAmount()
    {
        return $this->whereMonth('disbursement_date', date('m'))->sum('disbursement_amount');
    }

}
