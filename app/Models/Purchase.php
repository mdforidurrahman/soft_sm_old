<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function purchaseable()
    {
        return $this->morphTo();
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Contact::class);
    }



    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    // public function shippingDetail()
    // {
    //     return $this->hasOne(ShippingDetail::class);
    // }
    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }
}
