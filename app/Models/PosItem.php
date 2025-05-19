<?php

namespace App\Models;

use App\Models\Pos;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pos_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pos_id',
        'product_id',
        'quantity',
        'unit_cost',
        'discount_percent',
        'unit_cost_before_tax',
        'tax_amount',
        'net_cost',
        'profit_margin',
        'unit_selling_price',
    ];

    /**
     * Relationships
     */
    public function pos()
    {
        return $this->belongsTo(Pos::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
