<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreSellRequest;
use App\Http\Requests\UpdateSellRequest;
use App\Models\Contact;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Sell;
use App\Models\SellItem;
use App\Models\SellShipping;
use App\Models\Store;
use App\Services\SaleTransactionService;
use App\Services\SMSService;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class SellsController extends Controller
{

    protected SaleTransactionService $transactionService;
    protected SMSService $smsService;

    public function __construct(SaleTransactionService $transactionService, SMSService $smsService)
    {
        $this->transactionService = $transactionService;
        $this->smsService = $smsService;
    }

  
  
  
    /**
     * Display a listing of the resource.
     */
    public function index()
    { 
      
    $user = Auth::user();

    if ($user->hasRole('admin')) {
        // Admin sees all customers, newest first
        $supplier = Contact::whereRole('customer')
            ->orderByDesc('id')
            ->get();
        $storeName = Store::where('status', 1)->latest()->get();
    } elseif ($user->hasRole('manager')) {
        // Manager sees only their store's customers, newest first
        $storeIds = $user->stores->pluck('id');
        $supplier = Contact::whereRole('customer')
            ->whereIn('store_id', $storeIds)
            ->orderByDesc('id')
            ->get();
        $storeName = $user->stores;
    } else {
        // For other roles, adjust as needed (e.g., only their created customers)
        $supplier = Contact::whereRole('customer')
            ->where('created_by', $user->id)
            ->orderByDesc('id')
            ->get();
        $storeName = $user->stores;
    }
      
      
 

        if (request()->ajax()) {
            // Get the current user's stores if not admin
            $userStores = Auth::user()->stores->pluck('id')->toArray();

            $data = Sell::query()
                ->when(!Auth::user()->hasRole('admin'), function ($query) use ($userStores) {
                    return $query->whereIn('store_id', $userStores);
                })
                ->latest();

            $role = auth()->user()?->roles()?->first()?->name ?? 'admin';

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('invoice', function ($row) use ($role) {
                    $showInvoiceUrl = route($role . '.sell.showInvoice', $row->id);
                    $downloadInvoiceUrl = route($role . '.sell.downloadInvoice', parameters: $row->id);

                    return '
                <div class="dropdown">
                    <button type="button" id="dropdownMenuButton-' . $row->id . '" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-invoice"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-' . $row->id . '">
                        <li><a class="dropdown-item" href="' . $showInvoiceUrl . '" target="_blank">Show Invoice</a></li>
                        <li><a class="dropdown-item" href="' . $downloadInvoiceUrl . '" target="_blank">Download Invoice</a></li>
                    </ul>
                </div>';
                })
                ->addColumn('customer_name', function ($row) {
                    $customer = Contact::find($row->customer_id);
                    if (!$customer) {
                        return 'N/A';
                    }

                    // Just display the customer name with their stored contact_id
                    return $customer->name . ' (' . $customer->contact_id . ')';
                })

                ->editColumn('sells_date', function ($row) {
                    return $row->sells_date;
                })
                ->addColumn('status', function ($row) {
                    return view('components.status-toggle', [
                        'id' => $row->id,
                        'model' => 'contact',
                        'status' => $row->status
                    ])->render();
                })
                ->addColumn('action', function ($row) {
                    return view('components.action-buttons', [
                        'id' => $row->id,
                        'model' => 'sells',
                        'editModal' => 'editModal',
                        'editModalRoute' => 'sell.edit',
                        'deleteRoute' => 'sell.destroy',
                        'returnModal' => 'returnSellModal',
                        'returnModalRoute' => 'sell.store',
                    ])->render();
                })
                ->rawColumns(['action', 'status', 'invoice'])
                ->make(true);
        }

        $products = Product::latest()->get();
        $categories = ProductCategory::latest()->get();

        return view('admin.sell.index', compact('supplier', 'products', 'storeName', 'categories'));
    }
  
  
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::User()->hasRole('admin')) {
            $storeName = Store::where('status', 1)->latest()->get();
        } else {
            $storeName = Auth::User()->stores;
        }

        $data = [
            'suppliers' => Contact::whereRole('customer')
                ->select('id', 'name')->get(),
            'stores' => $storeName,
            'business_locations' => $storeName,
            'products' => Product::latest()->get(),
            'payment_methods' => ['Cash', /* 'bKash', 'Nagad', 'Rocket', */ 'Bank Transfer', 'Credit Card', 'Cheque'],
            'payment_terms' => ['Due on Receipt', 'Net 15', 'Net 30', 'Net 45', 'Net 60'],
            'purchase_statuses' => ['Draft', 'Ordered', 'Received', 'Pending', 'Cancelled']
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreSellRequest $request)
    {
        try {
            Log::info($request->all());

            $data = $request->isJson() ? $request->json()->all() : $request->all();
            DB::beginTransaction();

            // Use provided total or calculate it
            $totalBeforeTax = $request->total_before_tax ?? $this->calculateTotalBeforeTax($request->items);
            $discountAmount = 0;
            $discountType = $request->discount_type;
            $discountPercentage = 0;

            if ($discountType === 'fixed') {
                $discountAmount = $request->discount_amount;
                $discountPercentage = 0;
            } elseif ($discountType === 'percentage') {
                // Bug fix: tax should be added after calculating discount on totalBeforeTax
                $discountAmount = ($totalBeforeTax * $request->discount_amount) / 100;
                $discountPercentage = $request->discount_amount;
            }

            // Use provided net_total or calculate it
            $netTotal = $request->net_total ??
                ($totalBeforeTax - $discountAmount + ($request->tax_amount ?? 0) +
                    ($request->shipping_cost ?? 0));

            // Ensure advance_balance doesn't exceed net_total
            $advanceBalance = min($request->advance_balance ?? 0, $netTotal);

            // Calculate payment due
            $paymentDue = $netTotal - $advanceBalance;

            // Set payment status based on payment due
            $paymentStatus = $request->payment_status;
            if ($paymentDue <= 0) {
                $paymentStatus = 'completed';
            } elseif ($advanceBalance > 0) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'pending';
            }

            $invoiceNumber = strtoupper(bin2hex(random_bytes(15)));

            // Create reference number if not provided
            $reference_no = $request->reference_no ?: strtoupper(bin2hex(random_bytes(8)));

            $sell = Sell::create([
                'store_id' => $request->store_id,
                'customer_id' => $request->customer_id,
                'reference_no' => $reference_no,
                'invoice_no' => $invoiceNumber,
                'sell_date' => $request->sell_date,
                'sell_status' => $request->sell_status,
                'payment_term' => $request->pay_term,
                'payment_term_type' => $request->pay_term_type,
                'total_before_tax' => $totalBeforeTax,
                'tax_amount' => $request->tax_amount ?? 0,
                'discount_type' => $request->discount_type,
                'discount_amount' => $discountAmount,
                'discount_percentage' => $discountPercentage,
                'net_total' => $netTotal,
                'advance_balance' => $advanceBalance,
                'payment_due' => $paymentDue,
                'payment_status' => $paymentStatus,
                'payment_due_date' => $request->payment_due_date,
            ]);

            // Create sell items and validate stock
            foreach ($request->items as $item) {
                // Check if product exists and has enough stock
                $product = Product::find($item['product_id']);

                if (!$product) {
                    throw new \Exception("Product with ID {$item['product_id']} not found");
                }

                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Not enough stock for product: {$product->name}. Available: {$product->quantity}, Requested: {$item['quantity']}");
                }

                $sellItem = new SellItem([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost_before_tax' => $item['unit_cost_before_tax'] ?? $item['unit_cost'],
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'net_cost' => $item['net_cost'],
                ]);

                $sell->items()->save($sellItem);

                // Update product stock
                $product->decrement('quantity', $item['quantity']);
            }

            // Create shipping details if provided
            $trackingNumber = strtoupper(bin2hex(random_bytes(15)));
            if ($request->filled('shipping_address')) {
                $sell->shippingDetail()->create([
                    'shipping_address' => $request->shipping_address,
                    'shipping_method' => $request->shipping_method,
                    'shipping_cost' => $request->shipping_cost,
                    'expected_delivery_date' => $request->expected_delivery_date,
                    'tracking_number' => $trackingNumber,
                    'shipping_status' => $request->shipping_status,
                ]);
            }

            // Only create payment record if advance balance is greater than zero
            if ($advanceBalance > 0) {
                $sell->payments()->create([
                    'amount' => $advanceBalance,
                    'paid_on' => now(),
                    'payment_method' => $request->payment_method,
                    'payment_account' => $request->payment_account,
                    'transaction_reference' => strtoupper(bin2hex(random_bytes(8))),
                    'payment_status' => 'completed', // Payment record itself is completed
                ]);

                // Record sale transaction
                $transaction = $this->transactionService->recordSale(
                    $request->store_id,
                    $advanceBalance,
                    'Sales',
                    'Sales Record for ' . $sell->id,
                    Auth::id(),
                    $sell->id
                );
            }

            // Only record discount as expense if there is a discount
            if ($discountAmount > 0) {
                $this->transactionService->recordExpense(
                    $sell->store_id,
                    $discountAmount,
                    'Discount',
                    'Expense for Discount Record for Sell ' . $sell->id,
                    Auth::id(),
                    $sell->id
                );
            }



            // Prepare sale details for SMS
            $saleDetails = [
                'order_id' => $sell->id,
                'amount' => $netTotal,
                'paid_amount' => $advanceBalance,
                'paymentDue' => $paymentDue,
                'discount' => $discountAmount,
            ];

            // Send SMS notification if customer has phone
            if ($sell->customer && $sell->customer->phone) {
                $response = $this->smsService->sendSaleConfirmation(
                    $sell->customer->phone,
                    $sell->customer->name,
                    $saleDetails
                );
            }

            DB::commit();
            $sell->load('items', 'payments', 'shippingDetail');

            return $this->success(['id' => $sell->id], 'Sell created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($documentPath) && $request->hasFile('document')) {
                Storage::delete($documentPath);
            }
            return $this->error('Something Went Wrong: ', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $sell = Sell::with([
                'items.product' => function ($query) {
                    $query->select('id', 'name', 'sku', 'price');
                },
                'shippingDetail',
                'payments'
            ])->findOrFail($id);


            // Transform the items to include product name
            $sell->items->transform(function ($item) {
                return [
                    'sell_id' => $item->purchase_id,
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

            // dd($sell);

            return response()->json([
                'success' => true,
                'data' => $sell
            ]);
        } catch (\Exception $e) {
            return $this->error('Something Went Wrong : ', $e->getMessage(), 500);
        }
    }

/**
 * Update the specified resource in storage.
 */
public function update(UpdateSellRequest $request, $id) {
    try {
        // Check if the user is an admin
        if (!Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this sale.'
            ], 403);
        }

        // Proceed with the update logic if the user is an admin
        $data = $request->isJson() ? $request->json()->all() : $request->all();
        DB::beginTransaction();

        $sell = Sell::findOrFail($id);

        // Existing update logic
        $totalBeforeTax = $request->total_before_tax ?? $this->calculateTotalBeforeTax($request->items);
        $discountAmount = 0;
        $discountType = $request->discount_type;
        $discountPercentage = 0;

        if ($discountType === 'fixed') {
            $discountAmount = $request->discount_amount;
            $discountPercentage = 0;
        } elseif ($discountType === 'percentage') {
            $discountAmount = (($totalBeforeTax + $request->tax_amount) * $request->discount_amount) / 100;
            $discountPercentage = $request->discount_amount;
        }

        $netTotal = $request->edit_net_total ?? ($totalBeforeTax - $discountAmount + $request->tax_amount + ($request->shipping_cost ?? 0));
        $paymentDue = $netTotal - ($request->advance_balance ?? 0);

        // Update sell record
        $sell->update([
            'store_id' => $request->store_id,
            'customer_id' => $request->customer_id,
            'sell_date' => $request->sell_date,
            'sell_status' => $request->sell_status,
            'payment_term' => $request->edit_pay_term ?? $request->pay_term,
            'payment_term_type' => $request->edit_pay_term_type ?? $request->pay_term_type,
            'reference_no' => $request->reference_no,
            'total_before_tax' => $totalBeforeTax,
            'tax_amount' => $request->tax_amount ?? 0,
            'discount_type' => $request->discount_type,
            'discount_amount' => $discountAmount,
            'discount_percentage' => $discountPercentage,
            'net_total' => $netTotal,
            'advance_balance' => $request->advance_balance ?? 0,
            'payment_due' => $paymentDue,
            'payment_status' => $request->payment_status,
            'payment_due_date' => $request->payment_due_date,
        ]);

        // Continue with your existing logic for items, shipping, etc.
        DB::commit();
        return $this->success(['id' => $sell->id], 'Sell updated successfully');
    } catch (\Exception $e) {
        DB::rollBack();
        return $this->error('Something Went Wrong: ', $e->getMessage(), 500);
    }
}
/**
 * Remove the specified resource from storage.
 */
public function destroy($id)
{
    try {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this sale.'
            ], 403);
        }

        DB::beginTransaction();

        $sell = Sell::findOrFail($id);
        
        // This will automatically delete all related items, payments, and shipping
        // due to the cascading foreign key constraints
        $sell->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Sale and all related records deleted successfully.'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Error deleting sale: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage()
        ], 500);
    }
}


public function downloadInvoice($id)
{
    try {
        // Correct variable name and load all necessary relationships
        $sell = Sell::with([
            'store',
            'customer', // Changed from 'contact' to match your relationship name
            'items.product',
            'shippingDetail',
            'payments'
        ])->findOrFail($id);

        // Generate PDF with proper error handling
        $pdf = PDF::loadView('admin.sell.invoice-pdf', compact('sell'))
            ->setPaper('a4')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10);

        $filename = "invoice_{$sell->invoice_no}.pdf";

        return $pdf->download($filename);
    } catch (\Exception $e) {
        Log::error("Error generating invoice PDF: " . $e->getMessage());
        return back()->with('error', 'Unable to generate invoice PDF');
    }
}
  

    /**
     * Show the invoice in a modal.
     */
public function showInvoice($id)
{
    try {
        // Load all necessary relationships with proper error handling
        $sell = Sell::with([
            'store',
            'customer', // Changed from 'contact' to match your relationship name
            'items.product',
            'shippingDetail',
            'payments'
        ])->findOrFail($id);

        return view('admin.sell.invoice-modal', compact('sell'))->render();
    } catch (\Exception $e) {
        Log::error("Error loading invoice: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Unable to load invoice details'
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
            return ($total - ($discountPercentage ?? 0));
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
    /**
     * Search customers for sell context (sells + contacts).
     */


    /**
     * Search customers (contacts only).
     */
    public function searchCustomers(Request $request)
    {
        try {
            $searchTerm = $request->input('search');

            $query = Contact::query();

            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('contact_id', 'LIKE', "%{$searchTerm}%");
                });
            }

            $customers = $query->get();

            return response()->json([
                'success' => true,
                'data' => $customers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

