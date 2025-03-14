<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SCVCache extends Model
{
    protected $fillable = ['data', 'param', 'key'];

    protected $casts = [
        'data' => 'array',
    ];

    public function getCache($param, $key)
    {
        return $this->where('param', $param)
                ->where('key', $key)
                ->latest()
                ->first()?->data;
    }

    public function setCache($data, $param, $key)
    {
        $this->updateOrCreate(
            ['param' => $param, 'key' => $key],
            ['data' => $data]
        );
    }
}
