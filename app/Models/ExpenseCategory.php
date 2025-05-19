<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    // Table name (if it's not the default plural form)
    protected $table = 'expense_category';

    // Fillable fields for mass assignment
    protected $fillable = [
        'name',
        'code',
        'is_subcategory',
        'parent_id',
    ];

    /**
     * Relationship: Parent Category
     * An expense category may belong to a parent category.
     */
    public function parentCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'parent_id');
    }

    /**
     * Define a relationship to get the subcategories, if needed.
     */
    public function subcategories()
    {
        return $this->hasMany(ExpenseCategory::class, 'parent_id');
    }

    public function editCategory($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        return response()->json($category);
    }

    // Update a category
    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:expense_categories,code,' . $id,
        ]);

        $category = ExpenseCategory::findOrFail($id);
        $category->name = $request->name;
        $category->code = $request->code;
        $category->is_subcategory = $request->has('is_subcategory');
        $category->parent_id = $request->parent_id;
        $category->save();

        return response()->json(['success' => 'Category updated successfully!']);
    }

    // Delete a category
    public function deleteCategory($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        $category->delete();

        return response()->json(['success' => 'Category deleted successfully!']);
    }

}
