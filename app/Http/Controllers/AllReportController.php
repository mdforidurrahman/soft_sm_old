<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sell;
use App\Models\Store;
use App\Models\Expense;
use App\Models\Purchase;
use Barryvdh\DomPDF\PDF;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Exports\ProfitLossExport;
use Maatwebsite\Excel\Facades\Excel;


class AllReportController extends Controller
{
    public function profitLoss()
    {
        $stores = Store::select('id', 'name')->get();
        return view('admin.reports.profit-loss', compact('stores'));
    }

    public function getProfitLossData(Request $request)
    {
        // Validate request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'store_id' => 'nullable|exists:stores,id'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $storeId = $request->store_id;

        // Base store condition
        $storeCondition = $storeId ? [['store_id', '=', $storeId]] : [];

        // Get main report data
        $reportData = $this->calculateReportData($startDate, $endDate, $storeCondition);

        // Get chart data
        $chartData = $this->getChartData($startDate, $endDate, $storeId);

        // Combine the data
        $response = array_merge($reportData, ['chart_data' => $chartData]);

        // return response()->json($response);

        // return $response;
        $stores = Store::select('id', 'name')->get();

        return view('admin.reports.profit-loss', compact('response', 'stores'));
    }

    private function calculateReportData($startDate, $endDate, $storeCondition)
    {
        // Calculate Sales Revenue
        $sales = Sell::where([
            ['sell_date', '>=', $startDate],
            ['sell_date', '<=', $endDate]
        ])
            ->where($storeCondition)
            ->where('payment_status', '!=', 'cancelled')
            ->select(
                DB::raw('COALESCE(SUM(total_before_tax), 0) as gross_sales'),
                DB::raw('COALESCE(SUM(tax_amount), 0) as sales_tax'),
                DB::raw('COALESCE(SUM(discount_amount), 0) as sales_discount'),
                DB::raw('COALESCE(SUM(net_total), 0) as net_total')
            )
            ->first();

        // Calculate Purchase Costs
        $purchases = Purchase::where([
            ['purchase_date', '>=', $startDate],
            ['purchase_date', '<=', $endDate]
        ])
            ->where($storeCondition)
            ->where('purchase_status', '!=', 'cancelled')
            ->select(
                DB::raw('COALESCE(SUM(total_before_tax), 0) as gross_purchases'),
                DB::raw('COALESCE(SUM(tax_amount), 0) as purchase_tax'),
                DB::raw('COALESCE(SUM(discount_amount), 0) as purchase_discount'),
                DB::raw('COALESCE(SUM(net_total), 0) as net_purchases')
            )
            ->first();

        // Calculate Expenses
        $expenses = Expense::where([
            ['expense_date', '>=', $startDate],
            ['expense_date', '<=', $endDate]
        ])
            ->where($storeCondition)
            ->where('status', 1)
            ->select(
                DB::raw('COALESCE(SUM(total_amount), 0) as total_expenses')
            )
            ->first();

        // Calculate previous period data for growth comparison
        $previousStartDate = (clone $startDate)->subDays($endDate->diffInDays($startDate) + 1);
        $previousEndDate = (clone $startDate)->subDay();

        $previousPeriodData = $this->calculatePreviousPeriodData(
            $previousStartDate,
            $previousEndDate,
            $storeCondition
        );

        // Calculate growth percentages
        $growthRates = $this->calculateGrowthRates($sales, $purchases, $expenses, $previousPeriodData);

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'sales' => [
                'gross' => round($sales->gross_sales, 2),
                'tax' => round($sales->sales_tax, 2),
                'discount' => round($sales->sales_discount, 2),
                'net' => round($sales->net_total, 2),
                'growth' => $growthRates['sales']
            ],
            'costs' => [
                'purchases' => [
                    'gross' => round($purchases->gross_purchases, 2),
                    'tax' => round($purchases->purchase_tax, 2),
                    'discount' => round($purchases->purchase_discount, 2),
                    'net' => round($purchases->net_purchases, 2)
                ],
                'expenses' => round($expenses->total_expenses, 2),
                'total_costs' => round($purchases->net_purchases + $expenses->total_expenses, 2),
                'growth' => $growthRates['costs']
            ],
            'summary' => [
                'gross_profit' => round($sales->net_total - $purchases->net_purchases, 2),
                'net_profit' => round($sales->net_total - ($purchases->net_purchases + $expenses->total_expenses), 2),
                'growth' => $growthRates['profit']
            ]
        ];
    }
    private function getChartData($startDate, $endDate, $storeId)
    {
        $period = CarbonPeriod::create($startDate, '1 day', $endDate);
        $dates = [];
        $salesData = [];
        $costsData = [];
        $profitData = [];

        // Prepare date ranges for the chart
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        // Get daily sales data
        $dailySales = Sell::where('sell_status', '!=', 'cancelled')
            ->whereBetween('sell_date', [$startDate, $endDate])
            ->when($storeId, function ($query) use ($storeId) {
                return $query->where('store_id', $storeId);
            })
            ->groupBy('sell_date')
            ->select('sell_date', DB::raw('SUM(net_total) as daily_sales'))
            ->pluck('daily_sales', 'sell_date')
            ->toArray();

        // Get daily costs data (purchases + expenses)
        $dailyCosts = DB::table(function ($query) use ($startDate, $endDate, $storeId) {
            $query->from('purchases')
                ->select(
                    'purchase_date as date',
                    DB::raw('SUM(net_total) as daily_cost')
                )
                ->where('purchase_status', '!=', 'cancelled')
                ->whereBetween('purchase_date', [$startDate, $endDate])
                ->when($storeId, function ($q) use ($storeId) {
                    return $q->where('store_id', $storeId);
                })
                ->groupBy('purchase_date')
                ->union(
                    DB::table('expenses')
                        ->select(
                            'expense_date as date',
                            DB::raw('SUM(total_amount) as daily_cost')
                        )
                        ->where('status', 1)
                        ->whereBetween('expense_date', [$startDate, $endDate])
                        ->when($storeId, function ($q) use ($storeId) {
                            return $q->where('store_id', $storeId);
                        })
                        ->groupBy('expense_date')
                );
        }, 'daily_costs')
            ->groupBy('date')
            ->select('date', DB::raw('SUM(daily_cost) as total_cost'))
            ->pluck('total_cost', 'date')
            ->toArray();

        // Prepare data for the chart
        foreach ($dates as $date) {
            $sale = $dailySales[$date] ?? 0;
            $cost = $dailyCosts[$date] ?? 0;

            $salesData[] = round($sale, 2);
            $costsData[] = round($cost, 2);
            $profitData[] = round($sale - $cost, 2);
        }

        return [
            'labels' => $dates,
            'sales' => $salesData,
            'costs' => $costsData,
            'profit' => $profitData
        ];
    }

    private function calculatePreviousPeriodData($startDate, $endDate, $storeCondition)
    {
        // Similar queries as above but for previous period
        $sales = Sell::where([
            ['sell_date', '>=', $startDate],
            ['sell_date', '<=', $endDate]
        ])
            ->where($storeCondition)
            ->where('sell_status', '!=', 'cancelled')
            ->sum('net_total');

        $purchases = Purchase::where([
            ['purchase_date', '>=', $startDate],
            ['purchase_date', '<=', $endDate]
        ])
            ->where($storeCondition)
            ->where('purchase_status', '!=', 'cancelled')
            ->sum('net_total');

        $expenses = Expense::where([
            ['expense_date', '>=', $startDate],
            ['expense_date', '<=', $endDate]
        ])
            ->where($storeCondition)
            ->where('status', 1)
            ->sum('total_amount');

        return [
            'sales' => $sales,
            'costs' => $purchases + $expenses,
            'profit' => $sales - ($purchases + $expenses)
        ];
    }

    private function calculateGrowthRates($sales, $purchases, $expenses, $previousPeriodData)
    {
        $currentSales = $sales->net_total;
        $currentCosts = $purchases->net_purchases + $expenses->total_expenses;
        $currentProfit = $currentSales - $currentCosts;

        return [
            'sales' => $this->calculateGrowthPercentage($currentSales, $previousPeriodData['sales']),
            'costs' => $this->calculateGrowthPercentage($currentCosts, $previousPeriodData['costs']),
            'profit' => $this->calculateGrowthPercentage($currentProfit, $previousPeriodData['profit'])
        ];
    }

    private function calculateGrowthPercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / abs($previous)) * 100, 2);
    }


    public function exportProfitLossPDF(Request $request)
    {
        // Validate request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'store_id' => 'nullable|exists:stores,id'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $storeId = $request->store_id;

        // Get store information if specific store is selected
        $store = $storeId ? Store::find($storeId) : null;

        // Base store condition for queries
        $storeCondition = $storeId ? [['store_id', '=', $storeId]] : [];

        // Get report data
        $reportData = $this->calculateReportData($startDate, $endDate, $storeCondition);

        // Get chart data for the trend graph
        $chartData = $this->getChartData($startDate, $endDate, $storeId);

        $chartImage = $this->generateChartImage($chartData);

        // Prepare data for the PDF
        $data = [
            'report_data' => $reportData,
            'chart_data' => $chartData,
            'chart_image' => $chartImage,
            'period' => [
                'start' => $startDate->format('M d, Y'),
                'end' => $endDate->format('M d, Y')
            ],
            'store' => $store,
            'generated_at' => now()->format('M d, Y H:i:s')
        ];

        // Generate PDF
        $pdf = PDF::loadView('admin.reports.profit-loss-pdf', $data);
        $pdf->setPaper('a4');

        // Generate filename
        $filename = 'profit_loss_report_';
        $filename .= $store ? $store->name . '_' : 'all_stores_';
        $filename .= $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d');
        $filename .= '.pdf';

        // Return PDF for download
        return $pdf->download($filename);
    }

    public function exportProfitLossExcel(Request $request)
    {
        // Validate request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'store_id' => 'nullable|exists:stores,id'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $storeId = $request->store_id;

        // Get store information if specific store is selected
        $store = $storeId ? Store::find($storeId) : null;

        // Base store condition for queries
        $storeCondition = $storeId ? [['store_id', '=', $storeId]] : [];

        // Get report data
        $reportData = $this->calculateReportData($startDate, $endDate, $storeCondition);

        // Get chart data for the trend analysis
        $chartData = $this->getChartData($startDate, $endDate, $storeId);
        $reportData['chart_data'] = $chartData;

        // Prepare period data
        $period = [
            'start' => $startDate->format('M d, Y'),
            'end' => $endDate->format('M d, Y')
        ];

        // Generate filename
        $filename = 'profit_loss_report_';
        $filename .= $store ? $store->name . '_' : 'all_stores_';
        $filename .= $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d');
        $filename .= '.xlsx';

        return Excel::download(
            new ProfitLossExport($reportData, $period, $store),
            $filename
        );
    }
}
