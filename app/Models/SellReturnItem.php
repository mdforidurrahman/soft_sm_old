<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellReturnItem extends Model
{
    protected $guarded = [];

    public function sell()
    {
        return $this->belongsTo(Sell::class);
    }

    public function returnItems()
    {
        return $this->hasMany(SellReturnItem::class);
    }

    public function sellReturn()
    {
        return $this->belongsTo(SellReturn::class);
    }

    public function sellItem()
    {
        return $this->belongsTo(SellItem::class);
    }

    public function product()
    {
        return $this->through('sellItem')->has('product');
    }
}
