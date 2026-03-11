<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gateways extends Model
{
    protected $fillable = [
        'name',
        'priority',
        'api_key',
        'is_active'
    ];

    public function scopeOrdered($query){
        return $query->where('is_active', true)->orderBy('priority', 'asc');
    }
}
