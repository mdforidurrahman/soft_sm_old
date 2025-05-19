<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\ExpenseCategory;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;

class ExpenseCategoryController extends Controller
{
    public function expenseCategoryData(Request $request)
    {

        if ($request->ajax()) {
            $categories = ExpenseCategory::with('parentCategory')->get();

            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('parent_category', function($category) {
                    return $category->parentCategory ? $category->parentCategory->name : 'None';
                })
                ->addColumn('action', function($category) {
                    return '
                    <button class="btn btn-sm btn-primary edit-btn" data-id="' . $category->id . '">Edit</button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="' . $category->id . '">Delete</button>
                ';
                })
                ->make(true);
        }

        // Fetch all categories to pass to the view
        $categories = ExpenseCategory::all();
        return view('admin.expense.addcategory', compact('categories'));
    }

    // Store a new expense category
    public function storeCategory(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:expense_category',
        ]);

        // Create the new category
        ExpenseCategory::create([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return response()->json(['success' => 'Category added successfully!']);
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
            'code' => 'required|string|max:255|unique:expense_category,code,' . $id,
        ]);

        $category = ExpenseCategory::findOrFail($id);
        $category->name = $request->name;
        $category->code = $request->code;
        $category->save();

        return response()->json(['success' => 'Category updated successfully!']);
    }

    // Delete a category
    public function destroy($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        $category->delete();

        return response()->json(['success' => 'Category deleted successfully!']);
    }

}
