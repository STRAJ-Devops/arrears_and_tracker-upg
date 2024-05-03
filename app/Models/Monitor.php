<?php

namespace App\Models;

use App\Models\Scopes\MonitorScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#[ScopedBy(MonitorScope::class)]
class Monitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'phone',
        'activity',
        'marketing_date',
        'appraisal_date',
        'application_date',
        'staff_id',
    ];
}
