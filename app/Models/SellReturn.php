<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellReturn extends Model
{
    protected $guarded = [];

    protected $dates = ['return_date'];

    public function sell()
    {
        return $this->belongsTo(Sell::class);
    }

    public function returnItems()
    {
        return $this->hasMany(SellReturnItem::class);
    }

    public function supplier()
    {
        return $this->through('sells')->has('customer');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }
}
