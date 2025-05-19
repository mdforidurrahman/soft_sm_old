<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use App\Models\Contact;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pos extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id', 'location', 'transaction_date', 'quantity', 'subtotal', 'discount', 'invoiceNo', 'shippingAddress',
        'order_tax', 'shipping_cost', 'total', 'payment_method', 'transaction_status', 'created_by', 'updated_by'
    ];

    // Relationship with Contact
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    // Many-to-Many relationship with Product using pos_products table
    public function products()
    {
        return $this->belongsToMany(Product::class, 'pos_products', 'pos_id', 'product_id')
            ->withPivot('quantity', 'unit_cost', 'discount_percent', 'unit_cost_before_tax', 'tax_amount', 'net_cost', 'profit_margin', 'unit_selling_price')
            ->withTimestamps();
    }

    // Relationship with the seller (User model)
    public function seller()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship with Store model
    // In Pos.php (Pos Model)
    public function store()
    {
        return $this->belongsTo(Store::class);
    }


    // Relationship with User model for created_by
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship with User model for updated_by
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
