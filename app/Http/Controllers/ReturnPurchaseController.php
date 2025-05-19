<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ReturnPurchaseController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {
            $data = PurchaseReturn::with([
                'purchase.supplier',
                'returnItems.purchaseItem.product'
            ])
                ->select('purchase_returns.*')
                ->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('return_date', function ($row) {
                    return Carbon::parse($row->return_date)->format('d-m-Y');
                })
                ->editColumn('total_return_amount', function ($row) {
                    return number_format($row->total_return_amount, 2);
                })
                ->editColumn('status', function ($row) {
                    $statusBadge = [
                        'processed' => 'success',
                        'pending' => 'warning',
                        'refunded' => 'primary'
                    ];

                    return "<span class='badge bg-{$statusBadge[$row->status]}'>" . ucfirst($row->status) . "</span>";
                })
                ->addColumn('supplier', function ($row) {
                    return $row->purchase->supplier->name ?? 'N/A';
                })
                ->addColumn('purchase_reference', function ($row) {
                    return $row->purchase->reference_no ?? 'N/A';
                })
                ->addColumn('total_items', function ($row) {
                    return $row->returnItems->count();
                })
                ->addColumn('action', function ($row) {
                    return view('components.action-buttons', [
                        'id' => $row->id,
                        'model' => 'purchaseReturn',
                        // 'viewModal' => 'showModal',
                        // 'viewModalRoute' => 'return-purchase.show',
                        // 'editRoute' => 'return-purchase.edit',
                        // 'deleteRoute' => 'return-purchase.destroy'
                    ])->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.purchase.return');
    }

    public function show($id)
    {
        $purchaseReturn = PurchaseReturn::with(['purchase.supplier', 'returnItems.purchaseItem.product'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'purchase_return' => $purchaseReturn
        ]);
    }

    public function getPurchaseItems($purchaseId)
    {
        $purchase = Purchase::with('items.product')->findOrFail($purchaseId);

        return response()->json([
            'success' => true,
            'purchase' => $purchase,
            'returnable_items' => $purchase->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'max_returnable_quantity' => $item->quantity - $this->getAlreadyReturnedQuantity($item->id)
                ];
            })
        ]);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'purchase_id' => 'required|exists:purchases,id',
            'items' => 'required|array',
            'items.*.purchase_item_id' => 'required|exists:purchase_items,id',
            'items.*.quantity' => 'required|numeric|min:0'
        ]);
        DB::beginTransaction();
        try {

            Log::info($request->purchase_id);

            $purchaseReturn = PurchaseReturn::create([
                'purchase_id' => $request->purchase_id,
                'return_date' => now(),
                'reason' => $request->reason,
                'total_return_amount' => 0,
                'status' => 'processed',
                'notes' => $request->notes
            ]);

            $totalReturnAmount = 0;

            // Process Return Items
            foreach ($request->items as $returnItem) {
                $purchaseItem = PurchaseItem::findOrFail($returnItem['purchase_item_id']);

                // Validate return quantity
                $alreadyReturned = $this->getAlreadyReturnedQuantity($purchaseItem->id);
                if ($returnItem['quantity'] > ($purchaseItem->quantity - $alreadyReturned)) {
                    throw new \Exception('Return quantity exceeds available quantity');
                }

                // Create Return Item
                $purchaseReturnItem = PurchaseReturnItem::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'purchase_item_id' => $purchaseItem->id,
                    'quantity' => $returnItem['quantity'],
                    'unit_cost' => $purchaseItem->unit_cost,
                    'total_return_amount' => $returnItem['quantity'] * $purchaseItem->unit_cost
                ]);

                // Update Product Quantity
                $product = Product::findOrFail($purchaseItem->product_id);
                $product->decrement('quantity', $returnItem['quantity']);

                $totalReturnAmount += $purchaseReturnItem->total_return_amount;
            }

            // Update Total Return Amount
            $purchaseReturn->update(['total_return_amount' => $totalReturnAmount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Return processed successfully',
                'purchase_return_id' => $purchaseReturn->id
            ]);
        } catch (\Exception $e) {
            Log::error('Purchase Return Error: ' . $e->getMessage());
            Log::error('Request Data: ' . json_encode($request->all()));
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    private function getAlreadyReturnedQuantity($purchaseItemId)
    {
        return PurchaseReturnItem::where('purchase_item_id', $purchaseItemId)
            ->sum('quantity');
    }
}
