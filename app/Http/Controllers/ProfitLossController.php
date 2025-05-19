<?php

namespace App\Http\Controllers;

use App\Models\Sell;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Store;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitLossController extends Controller
{
    public function index()
    {
        $stores = Store::select('id', 'name')->where('status', 1)->get();
        return view('admin.reports.profit-loss', compact('stores'));
    }

    public function getData(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'store_id' => 'nullable|exists:stores,id'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $storeId = $request->store_id;

        // Get sales data
        $salesData = $this->getSalesData($startDate, $endDate, $storeId);

        // Get purchase data
        $purchaseData = $this->getPurchaseData($startDate, $endDate, $storeId);

        // Get expense data
        $expenseData = $this->getExpenseData($startDate, $endDate, $storeId);

        // Calculate totals and profit
        $totalCosts = $purchaseData['net_total'] + $expenseData['total'];
        $netProfit = $salesData['net_total'] - $totalCosts;

        // Get chart data
        $chartData = $this->getChartData($startDate, $endDate, $storeId);

        return response()->json([
            'sales' => [
                'gross' => round($salesData['gross_total'], 2),
                'tax' => round($salesData['tax_amount'], 2),
                'discount' => round($salesData['discount_amount'], 2),
                'net' => round($salesData['net_total'], 2)
            ],
            'costs' => [
                'purchases' => [
                    'gross' => round($purchaseData['gross_total'], 2),
                    'tax' => round($purchaseData['tax_amount'], 2),
                    'discount' => round($purchaseData['discount_amount'], 2),
                    'net' => round($purchaseData['net_total'], 2)
                ],
                'expenses' => round($expenseData['total'], 2),
                'total_costs' => round($totalCosts, 2)
            ],
            'summary' => [
                'net_profit' => round($netProfit, 2)
            ],
            'chart_data' => $chartData
        ]);
    }

    private function getSalesData($startDate, $endDate, $storeId)
    {
        return Sell::where('sell_status', '!=', 'cancelled')
            ->whereBetween('sell_date', [$startDate, $endDate])
            ->when($storeId, function($query) use ($storeId) {
                return $query->where('store_id', $storeId);
            })
            ->select(
                DB::raw('COALESCE(SUM(total_before_tax), 0) as gross_total'),
                DB::raw('COALESCE(SUM(tax_amount), 0) as tax_amount'),
                DB::raw('COALESCE(SUM(discount_amount), 0) as discount_amount'),
                DB::raw('COALESCE(SUM(net_total), 0) as net_total')
            )
            ->first()
            ->toArray();
    }

    private function getPurchaseData($startDate, $endDate, $storeId)
    {
        return Purchase::where('purchase_status', '!=', 'cancelled')
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->when($storeId, function($query) use ($storeId) {
                return $query->where('store_id', $storeId);
            })
            ->select(
                DB::raw('COALESCE(SUM(total_before_tax), 0) as gross_total'),
                DB::raw('COALESCE(SUM(tax_amount), 0) as tax_amount'),
                DB::raw('COALESCE(SUM(discount_amount), 0) as discount_amount'),
                DB::raw('COALESCE(SUM(net_total), 0) as net_total')
            )
            ->first()
            ->toArray();
    }

    private function getExpenseData($startDate, $endDate, $storeId)
    {
        return Expense::where('status', 1)
            ->where('is_refund', 0)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->when($storeId, function($query) use ($storeId) {
                return $query->where('store_id', $storeId);
            })
            ->select(DB::raw('COALESCE(SUM(total_amount), 0) as total'))
            ->first()
            ->toArray();
    }

    private function getChartData($startDate, $endDate, $storeId)
    {
        $period = CarbonPeriod::create($startDate, '1 day', $endDate);
        $dates = [];
        $salesData = [];
        $costsData = [];
        $profitData = [];

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $dates[] = $formattedDate;

            // Get daily sales
            $sales = $this->getDailySales($formattedDate, $storeId);

            // Get daily costs (purchases + expenses)
            $costs = $this->getDailyCosts($formattedDate, $storeId);

            $salesData[] = round($sales, 2);
            $costsData[] = round($costs, 2);
            $profitData[] = round($sales - $costs, 2);
        }

        return [
            'labels' => $dates,
            'sales' => $salesData,
            'costs' => $costsData,
            'profit' => $profitData
        ];
    }

    private function getDailySales($date, $storeId)
    {
        return Sell::where('sell_status', '!=', 'cancelled')
            ->whereDate('sell_date', $date)
            ->when($storeId, function($query) use ($storeId) {
                return $query->where('store_id', $storeId);
            })
            ->sum('net_total');
    }

    private function getDailyCosts($date, $storeId)
    {
        $purchases = Purchase::where('purchase_status', '!=', 'cancelled')
            ->whereDate('purchase_date', $date)
            ->when($storeId, function($query) use ($storeId) {
                return $query->where('store_id', $storeId);
            })
            ->sum('net_total');

        $expenses = Expense::where('status', 1)
            ->where('is_refund', 0)
            ->whereDate('expense_date', $date)
            ->when($storeId, function($query) use ($storeId) {
                return $query->where('store_id', $storeId);
            })
            ->sum('total_amount');

        return $purchases + $expenses;
    }
}
