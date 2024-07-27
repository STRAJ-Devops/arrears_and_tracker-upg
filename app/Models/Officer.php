<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Officer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $primaryKey = 'staff_id';

    public $incrementing = false;

    protected $fillable = [
        'staff_id',
        'names',
        'user_type',
        'username',
        'region_id',
        'branch_id',
        'password',
        'un_hashed_password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $guard_name = 'officer'; // Ensure this matches your guard name

    //an officer has many arrears
    public function arrears()
    {
        return $this->hasMany(Arrear::class, 'staff_id', 'staff_id');
    }

    //total principal arrears that an officer has
    public function totalPrincipalArrears()
    {
        return $this->arrears->sum('principal_arrears');
    }

    //total interest arrears that an officer has
    public function totalInterestArrears()
    {
        return $this->arrears->sum('outstanding_interest');
    }

    //total arrears that an officer has
    public function totalArrears()
    {
        return $this->totalPrincipalArrears() + $this->totalInterestArrears();
    }

    //clients in arrears that an officer has by number of days late
    public function clientsInArrears()
    {
        return $this->arrears->where('number_of_days_late', '>', 0)->count();
    }

    //total number of clients that an officer has based on the number of group members
    public function totalClients()
    {
        return $this->arrears->sum('number_of_group_members');
    }

    //an officer belongs to a region
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'region_id');
    }

    public function getAuthIdentifierName()
    {
        return 'username'; // Adjust if needed
    }

    public function getAuthIdentifier()
    {
        return $this->attributes['username'];
    }

    public function getAuthPassword()
    {
        return $this->attributes['password'];
    }

    //an officer can have many monitors
    public function monitors()
    {
        return $this->hasMany(Monitor::class, 'staff_id', 'staff_id');
    }

    //officerTarget relationship
    public function officerTarget()
    {
        return $this->hasOne(OfficerTarget::class, 'officer_id', 'staff_id');
    }
}
