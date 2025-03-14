<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardCache extends Model
{
    protected $fillable = ['data'];

    protected $casts = [
        'data' => 'array',
    ];

    public function getCache()
    {
        return $this->latest()->value('data');
    }

    public function setCache($data)
    {
        $this->create(['data' => $data]);
    }

    public function clearLastCache()
    {
        $this->latest()->first()?->delete();
    }
}
