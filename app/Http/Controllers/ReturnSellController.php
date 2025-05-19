<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sell;
use App\Models\Product;
use App\Models\SellItem;
use App\Models\SellReturn;
use Illuminate\Http\Request;
use App\Models\SellReturnItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ReturnSellController extends Controller
{


    public function index()
    {
        if (request()->ajax()) {
            $data = SellReturn::with([
                'sell.contact',
                'returnItems.sellItem.product'
            ])
                ->select('sell_returns.*')
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
                ->addColumn('customer', function ($row) {
                    return $row->sell->contact->name ?? 'N/A';
                })
                ->addColumn('sell_reference', function ($row) {
                    return $row->sell->reference_no ?? 'N/A';
                })
                ->addColumn('total_items', function ($row) {
                    return $row->returnItems->count();
                })
                ->addColumn('action', function ($row) {
                    return view('components.action-buttons', [
                        'id' => $row->id,
                        'model' => 'sellReturn',
                        // 'viewModal' => 'showModal',
                        // 'viewModalRoute' => 'return-sell.show',
                        // 'editRoute' => 'return-sell.edit',
                        // 'deleteRoute' => 'return-sell.destroy'
                    ])->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.sell.return');
    }

    public function show($id)
    {
        $sellReturn = SellReturn::with(['sell.customer', 'returnItems.sellItem.product'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'sell_return' => $sellReturn
        ]);
    }

    public function getSellItems($sellId)
    {
        $sell = Sell::with('items.product')->findOrFail($sellId);

        return response()->json([
            'success' => true,
            'sell' => $sell,
            'returnable_items' => $sell->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->net_cost,
                    'max_returnable_quantity' => $item->quantity - $this->getAlreadyReturnedQuantity($item->id)
                ];
            })
        ]);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'sell_id' => 'required|exists:sells,id',
            'items' => 'required|array',
            'items.*.sell_item_id' => 'required|exists:sell_items,id',
            'items.*.quantity' => 'required|numeric|min:0'
        ]);
        DB::beginTransaction();
        try {

            $sellReturn = SellReturn::create([
                'sell_id' => $request->sell_id,
                'return_date' => now(),
                'reason' => $request->reason,
                'total_return_amount' => 0,
                'status' => 'processed',
                'notes' => $request->notes
            ]);

            $totalReturnAmount = 0;

            // Process Return Items
            foreach ($request->items as $returnItem) {
                $sellItem = SellItem::findOrFail($returnItem['sell_item_id']);

                // Validate return quantity
                $alreadyReturned = $this->getAlreadyReturnedQuantity($sellItem->id);
                if ($returnItem['quantity'] > ($sellItem->quantity - $alreadyReturned)) {
                    throw new \Exception('Return quantity exceeds available quantity');
                }

                // Create Return Item
                $sellReturnItem = SellReturnItem::create([
                    'sell_return_id' => $sellReturn->id,
                    'sell_item_id' => $sellItem->id,
                    'quantity' => $returnItem['quantity'],
                    'unit_cost' => $sellItem->net_cost,
                    'total_return_amount' => $returnItem['quantity'] * $sellItem->net_cost
                ]);

                // Update Product Quantity

                $product = Product::findOrFail($sellItem->product_id);
                $product->decrement('quantity', $returnItem['quantity']);

                $totalReturnAmount += $sellReturnItem->total_return_amount;
            }

            // Update Total Return Amount
            $sellReturn->update(['total_return_amount' => $totalReturnAmount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sell Return processed successfully',
                'sell_return_id' => $sellReturn->id
            ]);
        } catch (\Exception $e) {
            Log::error('Sell Return Error: ' . $e->getMessage());
            Log::error('Request Data: ' . json_encode($request->all()));
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    private function getAlreadyReturnedQuantity($sellItemId)
    {
        return SellReturnItem::where('sell_item_id', $sellItemId)
            ->sum('quantity');
    }
}
