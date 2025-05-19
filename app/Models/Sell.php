<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sell extends Model
{
    protected $fillable = [
        'store_id',
        'customer_id',
        'reference_no',
        'invoice_no',
        'sell_date',
        'sell_status',
        'payment_term',
        'payment_term_type',
        'discount_type',
        'discount_amount',
        'discount_percentage',
        'total_before_tax',
        'net_total',
        'tax_amount',
        'advance_balance',
        'payment_due',
        'payment_status',
    ];
    public function sellable()
    {
        return $this->morphTo();
    }

    public function items()
    {
        return $this->hasMany(SellItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SellPayment::class);
    }

    public function shippingDetail()
    {
        return $this->hasOne(SellShippingDetail::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'customer_id');
    }

	public function customer() {
		return $this->belongsTo(Contact::class, 'customer_id');
	}

    // Sell Model


}