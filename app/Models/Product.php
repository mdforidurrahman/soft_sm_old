<?php

namespace App\Models;

use App\Models\Store;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'created_by',
        'updated_by',
        'name',
        'description',
        'image',
        'price',
        'quantity',
        'store_id',
        'min_stock',
        'category_id',
        'slug',
        'sku',
        'manage_stock',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    } //end method
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    } //end method

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    } //end method


    public function transfers()
    {
        return $this->hasMany(ProductTransfer::class, 'store_product_id');
    }
}
