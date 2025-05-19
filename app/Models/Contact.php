<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'father_name',
        'role',
        'sales_type',
        'installment',
        'district',
        'thana',
        'post_office',
        'village',
        'phone',
        'nid',
        'media_name',
        'media_number',
        'contact_id',
        'store_id',
      	'image',
		'finger_print',
		'signature',
		'status',
		'product_category_id'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }


    public function sells()
    {
        return $this->hasMany(Sell::class, 'customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function payments()
    {
        return $this->hasMany(CustomerPayment::class, 'customer_id');
    }
  
  
  protected static function boot()
{
    parent::boot();

    static::deleting(function ($contact) {
        foreach (['image', 'finger_print', 'signature'] as $field) {
            if ($contact->$field && file_exists(public_path($contact->$field))) {
                @unlink(public_path($contact->$field));
            }
        }
    });
}
}