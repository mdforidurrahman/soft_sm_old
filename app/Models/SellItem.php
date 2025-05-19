<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'sell_id',
        'product_id',
        'quantity',
        
        'unit_cost_before_tax',
        'tax_amount',
        'net_cost',
       
    ];
    protected $guarded = [];

    public function sell()
    {
        return $this->belongsTo(Sell::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
