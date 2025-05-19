<?php

namespace App\Http\Controllers;

use App\Models\PosItem;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Pos;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Carbon\Carbon; // Make sure to import Carbon at the top


use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $customers = Contact::where('role', 'customer')->get(); // Get customers
        $categories = ProductCategory::all(); // Get categories
        $stores= Store::all();

        // You can also load the products if you want to show them initially
        $products = Product::where('status', 1)->get();

        // return $products;


        return view('admin.pos.dashboard', compact('customers', 'categories', 'products','stores'));
    }
    public function product()
    {

        $products = Product::where('status', 1)->get();

        return response()->json($products);
    }

    public function report()
    {

        // Load related models: 'products', 'contact', 'store', and 'seller'
        $pos = Pos::with(['products', 'contact', 'store', 'seller'])->latest()->get();
        \Log::info('Data:', ['pos' => $pos]);

//        return $pos;
        if (request()->ajax()) {
            return DataTables::of($pos)
                ->addIndexColumn()
                ->editColumn('contact_id', function ($row) {
                    return $row->contact ? $row->contact->name : 'N/A';
                })
                ->addColumn('store_name', function ($row) {
                    return $row->location;
                })
                ->addColumn('seller_name', function ($row) {
                    return $row->seller ? $row->seller->name : 'N/A';
                })
                ->addColumn('product_names', function ($row) {
                    // Join all product names with commas
                    return $row->products->map(function ($product) {
                        return $product->name . ' (Qty: ' . $product->pivot->quantity . ')';
                    })->implode(', ');
                })
                ->addColumn('quantity', function ($row) {
                    return $row->quantity;
                })
                ->addColumn('subtotal', function ($row) {
                    return '$' . number_format($row->subtotal, 2);
                })
                ->addColumn('discount', function ($row) {
                    return '$' . number_format($row->discount, 2);
                })
                ->addColumn('order_tax', function ($row) {
                    return '$' . number_format($row->order_tax, 2);
                })
                ->addColumn('shipping_cost', function ($row) {
                    return '$' . number_format($row->shipping_cost, 2);
                })
                ->addColumn('total', function ($row) {
                    return '$' . number_format($row->total, 2);
                })
                ->addColumn('payment_method', function ($row) {
                    return ucfirst($row->payment_method);
                })
                ->addColumn('transaction_date', function ($row) {
                    return $row->transaction_date ? Carbon::parse($row->transaction_date)->format('Y-m-d H:i:s') : 'N/A';
                })

                ->make(true);
        }

        return view('admin.pos.report');
    }




    public function searchProduct(Request $request)
    {
        $query = $request->input('query');
        $products = Product::where('name', 'like', "%$query%")
            ->orWhere('sku', 'like', "%$query%")
            ->get();

        return response()->json($products);
    }
    public function productsByCategory( $categoryId)
    {
//        return $Id;
        $products = Product::where('category_id', $categoryId)->where('status', 1)->get();
        return response()->json($products);
    }
    public function store(Request $request)
    {
//        \Log::info('Received data:', $request->all());
        try {
            // Validate the request data
            $request->validate([
                'contact_id' => 'required|integer', // Ensure contact_id is provided and is an integer
                'location' => 'required|string',
                'transaction_date' => 'required|date',
                'subtotal' => 'required|numeric',
                'discount' => 'required|numeric',
                'order_tax' => 'required|numeric',
                'shipping_cost' => 'required|numeric',
                'total' => 'required|numeric',
                'payment_method' => 'required|string',
                'transaction_status' => 'required|string',
                'items' => 'required|string', // Items should be a valid JSON string
            ]);

            // Decode the items JSON string into an array
            $items = json_decode($request->items, true);

            // Validate if items are decoded correctly
            if (!is_array($items)) {
                throw new \Exception('Invalid items format.');
            }

            // Step 1: Create the Pos record
            $pos = Pos::create([
                'contact_id' => $request->contact_id,
                'location' => $request->location,
                'transaction_date' => $request->transaction_date,
                'quantity' => collect($items)->sum('quantity'), // Sum all quantities from items
                'subtotal' => $request->subtotal,
                'discount' => $request->discount,
                'order_tax' => $request->order_tax,
                'shipping_cost' => $request->shipping_cost,
                'shippingAddress' => $request->shippingAddress,
                'invoiceNo' => $request->invoiceNo,
                'total' => $request->total,
                'payment_method' => $request->payment_method,
                'transaction_status' => $request->transaction_status,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Step 2: Prepare an array of PosItem data, using the pos_id from the created Pos record
            $posItems = collect($items)->map(function ($item) use ($pos) {
                return [
                    'pos_id' => $pos->id, // Use the ID from the created Pos record
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'discount_percent' => $item['discount_percent'],
                    'unit_cost_before_tax' => $item['unit_cost_before_tax'],
                    'tax_amount' => $item['tax_amount'],
                    'net_cost' => $item['net_cost'],
                    'profit_margin' => $item['profit_margin'],
                    'unit_selling_price' => $item['unit_selling_price'],
                ];
            })->toArray();

            // Step 3: Use Eloquent to insert all PosItem records at once
            PosItem::insert($posItems);

            // Step 4: Return a JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Transaction stored successfully']);
            }

            session()->flash('success', 'Transaction stored successfully.');
            return response()->json(['message' => 'Transaction stored successfully'], 201);

        } catch (\Exception $e) {
//            \Log::error('Error:', ['message' => $e->getMessage()]);
            session()->flash('error', $e->getMessage());

            // Return a JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
            }

            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }


}
