<?php

namespace App\Models;

use App\Models\Scopes\ArrearScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sub_County;

#[ScopedBy(ArrearScope::class)]
class Arrear extends Model
{
    use HasFactory;
    protected $fillable = [
        'staff_id',
        'branch_id',
        'region_id',
        'product_id',
        'district_id',
        'subcounty_id',
        'village_id',
        'outsanding_principal',
        'outstanding_interest',
        'principal_arrears',
        'number_of_days_late',
        'number_of_group_members',
        'lending_type',
        'interest_in_arrears',
        'par',
        'gender',
        'customer_id',
        'amount_disbursed',
        'next_repayment_principal',
        'next_repayment_interest',
        'next_repayment_date',
        'dda',
        'group_id',
        'disbursement_date',
        'number_of_women',
        'group_name',
        'maturity_date',
    ];

    //an arrear belongs to an Officer
    public function officer()
    {
        return $this->belongsTo(Officer::class, 'staff_id', 'staff_id');
    }

    //an arrear belongs to a Branch
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

    //an arrear belongs to a Region
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'region_id');
    }

    //an arrear belongs to a Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    //an arrear belongs to a District
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
    }

    //an arrear belongs to a Subcounty

    public function sub_county()
    {
        return $this->belongsTo(Sub_county::class, 'subcounty_id', 'subcounty_id');
    }

    //an arrear belongs to a Village

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id', 'village_id');
    }

    //an arrear belongs to a Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
    //count the number of comments related to an arrear
    public function countComments()
    {
        return $this->comments->count();
    }

    //each product in arrear has targetClients
    public function ProductTarget()
    {
        return $this->hasMany(ProductTarget::class, 'product_id', 'product_id');
    }

    public static function sumPrincipalArrearsBeforeToday()
    {
        return static::where('number_of_days_late', '>', 0)
            ->sum('principal_arrears');
    }

}
