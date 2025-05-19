<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
{
    $productCategories = ProductCategory::latest()->get();

    if (Auth::User()->hasRole('admin')) {
        $storeName = Store::where('status', 1)->latest()->get();
    } else {
        $storeName = Auth::User()->stores;
    }

    if (request()->ajax()) {
        // Get the current user's stores if not admin
        $userStores = Auth::user()->stores->pluck('id')->toArray();
        
        $projects = Product::with(['user', 'category', 'store'])
            ->when(!Auth::user()->hasRole('admin'), function($query) use ($userStores) {
                return $query->whereIn('store_id', $userStores);
            })
            ->latest();

        $role = auth()->user()?->roles()?->first()?->name ?? 'admin';

        return DataTables::of($projects)
            ->addIndexColumn()
            ->editColumn('user_id', function ($row) {
                return $row->user->name;
            })
            ->addColumn('image', function ($row) {
                return '<img src="' . asset($row->image) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
            })
            ->addColumn('store_id', function ($row) {
                return $row['store']['name'];
            })
            ->addColumn('category_id', function ($row) {
                return $row['category']['name'];
            })
            ->addColumn('manage_stock', function ($row) {
                return $row->manage_stock == 1 ? 'In Stock' : 'Stock out';
            })
            ->addColumn('status', function ($row) {
                return view('components.status-toggle', [
                    'id' => $row->id,
                    'model' => 'product',
                    'status' => $row->status
                ])->render();
            })
            ->addColumn('action', function ($row) {
                return view('components.action-buttons', [
                    'id' => $row->id,
                    'model' => 'product',
                    'viewRoute' => 'product.show',
                    'editModal' => 'editModal',
                    'editModalRoute' => 'product.edit',
                    'deleteRoute' => 'product.destroy',
                ])->render();
            })
            ->rawColumns(['action', 'status', 'image', 'store_id', 'category_id', 'manage_stock'])
            ->make(true);
    }

    return view('admin.product.index', compact('productCategories', 'storeName'));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'description' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpeg,jpg,png|max:5120', // 5MB max
                'price' => 'required|numeric',
                'quantity' => 'required|integer',
                'store_id' => 'required|exists:stores,id',
                'min_stock' => 'required|integer',
                'category_id' => 'required|exists:product_categories,id', // Ensure category_id is validated
                'manage_stock' => 'nullable|boolean',
            ]);

            $product = new Product();
            $product->user_id = Auth::id();
            $product->created_by = Auth::id();
            $product->updated_by = Auth::id();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->quantity = $request->quantity;
            $product->store_id = $request->store_id;
            $product->min_stock = $request->min_stock; // Ensure this is set
            $product->category_id = $request->category_id; // Set the category_id here
            $product->slug = Str::slug($request->name);

            // Handle image upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $name_gen = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
                Image::make($file)->resize(300, 300)->save('upload/products/' . $name_gen);
                $product->image = 'upload/products/' . $name_gen;
            }

            // Generate unique SKU
            do {
                $sku = strtoupper('SKU-' . Str::random(4));
            } while (Product::where('sku', $sku)->exists());

            $product->sku = $sku;

            $product->save();
            return $this->success(['id' => $product->id], 'Product created successfully');
        } catch (Exception $e) {
            return $this->error('Something Went Wrong : ', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Product::with(['user', 'category', 'store'])->findOrFail($id);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Product::with(['user', 'category', 'store'])->findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $request->validate([
                'name' => 'required|string',
                'description' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpeg,jpg,png|max:5120', // 5MB max
                'price' => 'required|numeric',
                'quantity' => 'required|integer',
                'store_id' => 'required|exists:stores,id',
                'min_stock' => 'required|integer',
                'category_id' => 'required|exists:product_categories,id', // Ensure category_id is validated
                'manage_stock' => 'nullable|boolean',
            ]);

            $product->update([
                'user_id' => Auth::id(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'quantity' => $request->input('quantity'),
                'store_id' => $request->input('store_id'),
                'min_stock' => $request->input('min_stock'),
                'manage_stock' => $request->input('manage_stock'),
                'category_id' => $request->input('category_id'),
                'slug' => Str::slug($request->input('name')),
            ]);


            // Handle image upload
            if ($request->hasFile('image')) {
                //delete old image
                if ($product->image) {
                    $old_image = public_path($product->image);
                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }
                }

                $file = $request->file('image');
                $name_gen = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
                Image::make($file)->resize(300, 300)->save('upload/products/' . $name_gen);
                $updateImgUrl = 'upload/products/' . $name_gen;
                $product->update([
                    'image' => $updateImgUrl
                ]);
                // $product->image = 'upload/products/' . $name_gen;
                // $product->save();
            }


            return $this->success(['id' => $product->id], 'Product updated successfully');
        } catch (Exception $e) {
            return $this->error('Something Went Wrong : ', $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $data = Product::findOrFail($id);

            if ($data->image) {
                $old_image = public_path($data->image);
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }

            $data->delete();

            return response()->json(['success' => true, 'message' => 'Product deleted successfully']);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }


	public function getProductsByStore(Request $request)
	{
		$storeId = $request->input('store_id');
		$categoryId = $request->input('category_id');
		$searchTerm = $request->input('search');

		// Validate store ID is required
		if (!$storeId) {
			return response()->json(['products' => []]);
		}

		$query = Product::where('store_id', $storeId);

		// Add category filter if provided
		if ($categoryId) {
			$query->where('category_id', $categoryId);
		}

		// Add search functionality
		if ($searchTerm) {
			$query->where(function($q) use ($searchTerm) {
				$q->where('name', 'LIKE', "%{$searchTerm}%")
					->orWhere('sku', 'LIKE', "%{$searchTerm}%");
			});
		}

		$products = $query->get();

		return response()->json([
			'products' => $products
		]);
	}
}
