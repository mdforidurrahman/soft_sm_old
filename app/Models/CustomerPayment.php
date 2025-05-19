<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{

    protected $fillable = [
        'customer_id',
        'amount',
        'payment_method',
        'paid_on',
        'payment_account_id',
        'document',
        'payment_note',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_on' => 'datetime'
    ];

    public function customer()
    {
        return $this->belongsTo(Contact::class, 'customer_id');
    }

//    public function paymentAccount()
//    {
//        return $this->belongsTo(PaymentAccount::class);
//    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
