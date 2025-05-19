<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessLocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id',
        'landmark',
        'city',
        'zip_code',
        'state',
        'country',
        'status',
        'created_by',
        'updated_by'
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
