<?php

namespace App\Models;

use App\Models\Scopes\SaleScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EllGreen\LaravelLoadFile\Laravel\Traits\LoadsFiles;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy(SaleScope::class)]
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
        'number_of_group_members',
        'group_id',
        'number_of_women'
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

    public function getDisbursementDateAttribute($value)
    {
        return Carbon::parse($value)->toDateString();
    }

}
