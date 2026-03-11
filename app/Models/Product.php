<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price'
    ];

    public function transactions(){
        return $this->belongsToMany(Transactions::class, 'transaction_products')
                    ->withPivot('quantity', 'unit_price')
                    ->withTimestamps();
    }
}
