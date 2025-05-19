<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'stripe_id',
        // 'stripe_product_id',
        // 'amount',
        // 'interval',
        // 'interval_count',
    ];
}
