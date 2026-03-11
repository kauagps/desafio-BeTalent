<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $fillable = [
        'client_id',
        'gateway_id',
        'total_amount',
        'status',
        'external_id'
    ];

    public function client(){
        return $this->belongsTo(Clients::class);
    }

    public function gateway(){
        return $this->belongsTo(Gateways::class);
    }

    public function products(){
        return $this->belongsToMany(Product::class, 'transaction_products')
                    ->withPivot('quantity', 'unit_price')
                    ->withTimestamps();
    }

}
