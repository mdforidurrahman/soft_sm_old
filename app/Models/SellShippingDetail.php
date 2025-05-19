<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellShippingDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'sell_id',
        'shipping_date',
        'shipping_address',
        'shipping_method',
        'shipping_cost',
        'expected_delivery_date',
        'tracking_number',
        
    ];
}
