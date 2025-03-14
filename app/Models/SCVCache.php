<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SCVCache extends Model
{
    protected $fillable = ['data', 'param', 'key'];

    protected $casts = [
        'data' => 'array',
    ];
}
