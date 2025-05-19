<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Services\SaleTransactionService;
use Exception;
use App\Models\Store;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
// use App\Models\ShippingDetail;
use App\Models\PurchasePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;

class PurchaseController extends Controller
{

	protected SaleTransactionService $transactionService;

	public function __construct(SaleTransactionService $transactionService) {
		$this->transactionService = $transactionService;
	}

	/**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Purchase::with('purchaseItems', 'purchaseItems.product')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('contact_id', function ($row) {
                    return $row->contact_id;
                })
                ->editColumn('purchase_date', function ($row) {
                    return $row->purchase_date;
                })
                ->addColumn('products', function ($row) {
                    $productNames = $row->purchaseItems->map(function ($item) {
                        return $item->product->name ?? 'N/A'; // Fallback if product is null
                    })->implode('<br>'); // Join names with line breaks

                    return $productNames; // Return formatted string
                })
                ->addColumn('quantity', function ($row) {
                    return $row->purchaseItems->map(function ($item) {
                        return $item->quantity ?? 'N/A';
                    })->implode('<br>');

                })
                ->addColumn('status', function ($row) {
                    return view('components.status-toggle', [
                        'id' => $row->id,
                        'model' => 'sell',
                        'status' => $row->status
                    ])->render();
                })
                ->addColumn('action', function ($row) {
                    return view('components.action-buttons', [
                        'id' => $row->id,
                        'model' => 'sell',
                        'editModal' => 'editModal',
                        'editModalRoute' => 'purchase.edit',
                        'returnModal' => 'returnPurchaseModal',
                        'returnModalRoute' => 'purchase.store',
                        'deleteRoute' => 'purchase.destroy',
                    ])->render();
                })
                ->rawColumns(['action', 'status','products','quantity'])
                ->make(true);
        }


        $products = Product::select('id', 'name', 'sku', 'price')->get();
        $supplier = Contact::whereRole('supplier')->get();


        if (Auth::User()->hasRole('admin')) {
            $storeName = Store::where('status', 1)->latest()->get();
        } else {
            $storeName = Auth::User()->stores;
        }
		$categories = ProductCategory::where('status', 1)->get();

        return view('admin.purchase.index', compact('supplier', 'products', 'storeName','categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $data = [
            'suppliers' => Contact::whereRole('supplier')
                ->select('id', 'name')->get(),
            'business_locations' => Store::select('id', 'name')->get(),
            'products' => Product::select('id', 'name', 'sku', 'current_stock')->get(),
            'payment_methods' => ['Cash', 'Bank Transfer', 'Credit Card', 'Cheque'],
            'payment_terms' => ['Due on Receipt', 'Net 15', 'Net 30', 'Net 45', 'Net 60'],
            'purchase_statuses' => ['Draft', 'Ordered', 'Received', 'Pending', 'Cancelled'],
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {


        try {
            DB::beginTransaction();

            $data = $request->isJson() ? $request->json()->all() : $request->all();

            $validated = $this->getValidationFactory()
                ->make($data, $request->rules(), $request->messages())
                ->validate();


            // Handle document upload
            $documentPath = null;
            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('purchase_documents');
            }

            // Calculate totals
            $totalBeforeTax = $this->calculateTotalBeforeTax($data['items']);
            $discountAmount = $this->calculateDiscount(
                $totalBeforeTax,
                $request->discount_type,
                $request->discount_amount,
                $request->discount_percent
            );

            $netTotal = $totalBeforeTax - $discountAmount + ($request->shipping_cost ?? 0);
            $paymentDue = $netTotal - ($request->advance_balance ?? 0);

            // Create purchase


            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'store_id' => $validated['business_store_id'],
                'reference_no' => $validated['reference_no'],
                'purchase_date' => $validated['purchase_date'],
                'purchase_status' => $validated['purchase_status'],
                'payment_term' => $request->payment_term,
                'payment_term_type' => $request->payment_term_type,
                'document_path' => $documentPath,
                'advance_balance' => $request->advance_balance ?? 0,
                'payment_due' => $paymentDue,
                'payment_status' => $this->determinePaymentStatus($netTotal, $request->advance_balance ?? 0),
//                'payment_due_date' => $request->payment_due_date,
                'discount_type' => $request->discount_type,
                'discount_amount' => $discountAmount,
                'discount_percentage' => 12,
//                 'discount_percentage' => $request->discount_percent,
                'total_before_tax' => $totalBeforeTax,
                'tax_amount' => $request->tax_amount,
                'net_total' => $netTotal,
                'additional_notes' => $request->additional_notes,
            ]);

            // Create purchase items
            foreach ($validated['items'] as $item) {
                $unitCostBeforeTax = $item['unit_cost'] * (1 - ($item['discount_percent'] ?? 0) / 100);
                $netCost = $unitCostBeforeTax;

                $purchaseItem = new PurchaseItem([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'unit_cost_before_tax' => $unitCostBeforeTax,
                    'tax_amount' => 0, // Calculate if needed
                    'net_cost' => $netCost,
                    'profit_margin' => $item['profit_margin'],
                    'unit_selling_price' => $this->calculateSellingPrice($netCost, $item['profit_margin']),
                ]);

                $purchase->items()->save($purchaseItem);

                // Update product stock
                Product::where('id', $item['product_id'])
                    ->increment('quantity', $item['quantity']);
            }
            // Handle advance payment
            if ($request->advance_balance > 0) {
                PurchasePayment::create([
                    'purchase_id' => $purchase->id,
                    'amount' => $request->advance_balance,
                    'paid_on' => now(),
                    'payment_method' => $request->payment_method,
                    'payment_account' => $request->payment_account,
                    'payment_note' => $request->payment_note,
                    'payment_status' => 'completed',
                ]);
            }

	 $this->transactionService->recordExpense(
				$request->business_store_id,
				$request->advance_balance,
				"Purchase",
				'Purchase Expense for ' . $purchase->id,
				Auth::id(),
				$purchase->id
			);


            DB::commit();


            return $this->success(['id' => $purchase->id], 'Purchase created successfully');
        } catch (Exception $e) {
            DB::rollBack();

            if (isset($documentPath)) {
                Storage::delete($documentPath);
            }
            return $this->error('Something Went Wrong : ', $e->getMessage(), 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public
    function show(Purchase $purchase)
    {
        //
    }

    public function showReturn($purchaseId)
    {
        return view('admin.purchase.return', ['purchaseId' => $purchaseId]);
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit($id)
    {
        try {
            $purchase = Purchase::with([
                'items.product' => function ($query) {
                    $query->select('id', 'name');
                },
                'shippingDetail',
                'payments'
            ])->findOrFail($id);

            // Transform the items to include product name
            $purchase->items->transform(function ($item) {
                return [
                    'purchase_id' => $item->purchase_id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'discount_percent' => $item->discount_percent,
                    'unit_cost_before_tax' => $item->unit_cost_before_tax,
                    'tax_amount' => $item->tax_amount,
                    'net_cost' => $item->net_cost,
                    'profit_margin' => $item->profit_margin,
                    'unit_selling_price' => $item->unit_selling_price
                ];
            });

            // Fetch stores
            $stores = Store::select('id', 'name')->get();

            return response()->json([
                'success' => true,
                'data' => $purchase,
                'stores' => $stores
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving purchase details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdatePurchaseRequest $request, $id)
    {
        try {
            Log::info($request->all());

            DB::beginTransaction();
            $purchase = Purchase::findOrFail($id);

            // Calculate totals
            $totalBeforeTax = $this->calculateTotalBeforeTax($request->items);
            $discountAmount = $this->calculateDiscount(
                $totalBeforeTax,
                $request->discount_type,
                $request->discount_amount,
                $request->discount_percent
            );

            $netTotal = $totalBeforeTax - $discountAmount + ($request->shipping_cost ?? 0);
            $paymentDue = $netTotal - ($request->advance_balance ?? 0);

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'store_id' => $request->store_id,
                'reference_no' => $request->reference_no,
                'purchase_date' => $request->purchase_date,
                'purchase_status' => $request->purchase_status,
                'payment_term' => $request->payment_term,
                'payment_term_type' => $request->payment_term_type,
                'advance_balance' => $request->advance_balance ?? 0,
                'payment_due' => $paymentDue,
                'payment_status' => $this->determinePaymentStatus($netTotal, $request->advance_balance ?? 0),
                'payment_due_date' => $request->payment_due_date,
                'discount_type' => $request->discount_type,
                'discount_amount' => $discountAmount,
                'discount_percentage' => 12,
                'total_before_tax' => $totalBeforeTax,
                'tax_amount' => 0,
                'net_total' => $netTotal,
                'additional_notes' => $request->additional_notes,
            ]);

            // Get old purchase items for stock adjustment
            $oldItems = $purchase->items()->get();

            // Delete existing purchase items
            $purchase->items()->delete();

            // Create new purchase items
            foreach ($request->items as $item) {
                $unitCostBeforeTax = $item['unit_cost'] * (1 - ($item['discount_percent'] ?? 0) / 100);
                $netCost = $unitCostBeforeTax; // Add tax calculation if needed

                $purchaseItem = new PurchaseItem([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'unit_cost_before_tax' => $unitCostBeforeTax,
                    'tax_amount' => 0, // Calculate if needed
                    'net_cost' => $netCost,
                    'profit_margin' => $item['profit_margin'],
                    'unit_selling_price' => $this->calculateSellingPrice($netCost, $item['profit_margin']),
                ]);

                $purchase->items()->save($purchaseItem);

                // Find old item quantity for the same product
                $oldItem = $oldItems->firstWhere('product_id', $item['product_id']);
                $oldQuantity = $oldItem ? $oldItem->quantity : 0;

                // Adjust the stock by the difference
                $quantityDifference = $item['quantity'] - $oldQuantity;
                Product::where('id', $item['product_id'])
                    ->increment('quantity', $quantityDifference);
            }

            if ($request->advance_balance > 0) {
                // Delete existing payments if any
                $purchase->payments()->delete();

                PurchasePayment::create([
                    'purchase_id' => $purchase->id,
                    'amount' => $request->advance_balance,
                    'paid_on' => now(),
                    'payment_method' => $request->payment_method,
                    'payment_account' => $request->payment_account,
                    'payment_note' => $request->payment_note,
                    'payment_status' => 'completed',
                ]);
            }


			$updatedExpense = $this->transactionService->updateTransaction(
				$purchase->id,
				'expense',
				$request->advance_balance,
				"Purchase",
				'Expense for ' . $purchase->id,
				Auth::id()
			);

            DB::commit();

            return $this->success(['id' => $purchase->id], 'Purchase updated successfully');
        } catch (Exception $e) {
            return $this->error('Something Went Wrong : ', $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find the purchase
            $purchase = Purchase::with(['items', /*'shippingDetail',*/ 'payments'])->findOrFail($id);

            // Revert stock changes for each purchase item
            foreach ($purchase->items as $item) {
                Product::where('id', $item->product_id)
                    ->decrement('quantity', $item->quantity);
            }

            // Delete the document if exists
            if ($purchase->document_path) {
                Storage::delete($purchase->document_path);
            }

            // Delete related records
            $purchase->items()->delete();
            //$purchase->shippingDetail()->delete();
            $purchase->payments()->delete();

            // Delete the purchase
            $purchase->delete();

            DB::commit();


            return response()->json(['success' => true, 'message' => 'Purchase deleted successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    private function generateOrderNumber($prefix = "POS-")
    {
        $now = now();
        return $prefix . $now->format('ymd-Hi-') . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }


    public function addPayment(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_account' => 'nullable|string',
            'payment_note' => 'nullable|string',
            'paid_on' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $payment = PurchasePayment::create([
                'purchase_id' => $purchase->id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_account' => $validated['payment_account'],
                'payment_note' => $validated['payment_note'],
                'paid_on' => $validated['paid_on'],
            ]);

            // Update purchase payment status
            $totalPaid = $purchase->payments()->sum('amount');
            $newStatus = $this->determinePaymentStatus($purchase->net_total, $totalPaid);
            $purchase->update([
                'payment_status' => $newStatus,
                'payment_due' => $purchase->net_total - $totalPaid,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment added successfully',
                'payment' => $payment,
                'purchase' => $purchase->fresh(['payments']),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected function calculateTotalBeforeTax($items)
    {
        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        return collect($items)->sum(function ($item) {
            $quantity = floatval($item['quantity'] ?? 0);
            $unitCost = floatval($item['unit_cost'] ?? 0);
            $discountPercent = floatval($item['discount_percent'] ?? 0);

            $costAfterDiscount = $unitCost * (1 - $discountPercent / 100);
            return $quantity * $costAfterDiscount;
        });
    }

    private function calculateDiscount($total, $discountType, $discountAmount, $discountPercentage)
    {
        if (!$discountType) {
            return 0;
        }

        if ($discountType === 'fixed') {
            return $discountAmount ?? 0;
        }

        if ($discountType === 'percentage') {
            return ($total * ($discountPercentage ?? 0)) / 100;
        }

        return 0;
    }

    private function calculateSellingPrice($netCost, $profitMargin)
    {
        return $netCost * (1 + ($profitMargin / 100));
    }

    private function determinePaymentStatus($total, $paidAmount)
    {
        if ($paidAmount >= $total) {
            return 'paid';
        }
        if ($paidAmount > 0) {
            return 'partial';
        }
        return 'pending';
    }

    private function updateProductStock($productId, $quantity)
    {
        $product = Product::find($productId);
        if ($product) {
            $product->stock_quantity = $product->stock_quantity + $quantity;
            $product->save();
        }
    }

    /**
     * Validate file upload
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return bool
     */
    private function validateDocument($file)
    {
        $allowedMimes = ['pdf', 'csv', 'zip', 'doc', 'docx', 'jpeg', 'jpg', 'png'];
        $maxSize = 5 * 1024; // 5MB in kilobytes

        return $file->getSize() <= $maxSize * 1024 && // Convert KB to bytes
            in_array($file->getClientOriginalExtension(), $allowedMimes);
    }
}
