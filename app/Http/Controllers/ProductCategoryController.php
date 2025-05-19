<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Str;
use Yajra\DataTables\Utilities\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = ProductCategory::latest()->get();
        if (request()->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    return view('components.status-toggle', [
                        'id' => $row->id,
                        'model' => 'ProductCategory',
                        'status' => $row->status
                    ])->render();
                })
                ->addColumn('action', function ($row) {
                    return view('components.action-buttons', [
                        'id' => $row->id,
                        'model' => 'ProductCategory',
                        'editModal' => 'editModal',
                        'editModalRoute' => 'product-category.edit',
                        'deleteRoute' => 'product-category.destroy',
                    ])->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.product-category.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string'
            ]);

            $productCategory = new ProductCategory;
            $productCategory->name = $validatedData['name'];
            $productCategory->slug = Str::slug($validatedData['name']);
            $productCategory->created_at = now();

            $productCategory->save();

            return response()->json(['success' => true, 'message' => 'User deleted successfully']);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // $ProductCat = ProductCategory::whereId($id)->first();
        // return view('admin.product-category.show',compact('ProductCat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $ProductCat = ProductCategory::findOrFail($id);
        return response()->json($ProductCat);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string'
            ]);

            $productCategory = ProductCategory::findOrFail($id);
            $productCategory->name = $validatedData['name'];
            $productCategory->slug = Str::slug($validatedData['name']);
            $productCategory->updated_at = now();

            $productCategory->save();

            return response()->json(['message' => 'Product Category updated successfully', 'productCategory' => $productCategory]);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            // Find the product category
            $pcategory = ProductCategory::findOrFail($id);

            // Check if the category has associated products
            if ($pcategory->hasProducts()) {
                return response()->json(['error' => 'Product Category has associated products and cannot be deleted'], 400);
            }

            // Delete the category
            $pcategory->delete();

            return response()->json(['success' => true, 'message' => 'Product Category deleted successfully']);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);

        }
    }


}
