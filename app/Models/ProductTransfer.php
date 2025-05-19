<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTransfer extends Model
{


    protected $fillable = [
        'from_store_id',
        'to_store_id',
        'store_product_id',
        'quantity',
        'status'
    ];

    public function fromStore()
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function toStore()
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function storeProduct()
    {
        return $this->belongsTo(Product::class);
    }
}
