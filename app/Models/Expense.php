<?php

namespace App\Models;

use App\Models\User;
use App\Models\Contact;
use App\Models\Store;
use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'expense_category_id',
        'reference_no',
        'expense_date',
        'expense_for_id',
        'expense_for_contact',
        'document',
        'total_amount',
        'note',
        'is_refund',
        'paid_amount',
        'paid_date',
        'payment_method',
        'payment_note',
        'status',
    ];

    // Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class, 'expense_for_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'expense_for_contact');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

}
