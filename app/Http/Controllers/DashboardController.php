<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sell;
use App\Models\SellReturn;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'date_range' => 'nullable|string|in:today,yesterday,last_7_days,last_30_days,this_month,last_month,this_year,last_year,custom,all',
            'store_id' => 'nullable|exists:stores,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $dateRange = $validated['date_range'] ?? 'today';
        $selectedStoreId = $validated['store_id'] ?? null;

        // Get accessible stores based on user role
        $stores = Auth::user()->hasRole('admin')
            ? Store::latest()->get()
            : Auth::user()->stores;

        // Calculate date range
        [$startDate, $endDate] = $this->calculateDateRange($dateRange, $request);

        // Get statistics
        $statistics = $this->getStatistics($selectedStoreId, $dateRange, $startDate, $endDate);

        if ($request->ajax()) {
            return response()->json([
                'data' => $statistics,
                'dateRange' => $dateRange,
                'startDate' => $startDate?->format('Y-m-d'),
                'endDate' => $endDate?->format('Y-m-d'),
                'selectedStore' => $selectedStoreId
            ]);
        }

        return view('admin.index', [
            'data' => $statistics,
            'stores' => $stores
        ]);
    }

    private function calculateDateRange($dateRange, Request $request)
    {
        $startDate = null;
        $endDate = null;

        switch ($dateRange) {
            case 'today':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'yesterday':
                $startDate = now()->subDay()->startOfDay();
                $endDate = now()->subDay()->endOfDay();
                break;
            case 'last_7_days':
                $startDate = now()->subDays(7)->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'last_30_days':
                $startDate = now()->subDays(30)->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'this_month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'last_month':
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            case 'last_year':
                $startDate = now()->subYear()->startOfYear();
                $endDate = now()->subYear()->endOfYear();
                break;
            case 'custom':
                $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfDay();
                $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now()->endOfDay();
                break;
            default:
                $startDate = null;
                $endDate = null;
        }

        return [$startDate, $endDate];
    }

public function getStatistics($selectedStoreId, $dateRange, $startDate, $endDate)
{
    try {
        $authUserId = Auth::id();
        $isAdmin = Auth::user()->hasRole('admin');
        $statistics = [];

        // Query builder for date filtering
        $dateFilter = function ($query) use ($dateRange, $startDate, $endDate) {
            return $dateRange !== 'all' ? $query->whereBetween('created_at', [$startDate, $endDate]) : $query;
        };

        // Get Customers (visible to all roles)
     //   $totalCustomers = Contact::where('role', '=', 'customer')
       //     ->where(function ($query) use ($authUserId) {
       //         $query->where('created_by', $authUserId)
        //            ->orWhere('updated_by', $authUserId);
        //    })
        //    ->when($dateRange !== 'all', $dateFilter)
       //     ->count();
      
      
      
      

      
    //  $totalCustomers = Contact::where('role', 'customer')->count();

      $authUser = Auth::user();

if ($authUser->hasRole('admin')) {
    // Admin sees all customers
    $totalCustomers = Contact::where('role', 'customer')
        ->when($dateRange !== 'all', $dateFilter)
        ->count();
} elseif ($authUser->hasRole('manager')) {
    // Manager sees only customers for their store(s)
    // If user has many stores (relation), use pluck to get their IDs
    $storeIds = $authUser->stores->pluck('id')->toArray(); // Adjust as needed
    $totalCustomers = Contact::where('role', 'customer')
        ->whereIn('store_id', $storeIds)
        ->when($dateRange !== 'all', $dateFilter)
        ->count();
} else {
    // Default: no customers or further restrictions
    $totalCustomers = 0;
}
        $statistics[] = [
            'title' => 'Total Customers',
            'count' => $totalCustomers,
            'icon' => 'bx-user-circle',
            'color' => 'bg-gradient-ohhappiness'
        ];

        // Purchase stats (only for admin)
        if ($isAdmin) {
            // Get purchases
            $totalPurchases = Purchase::when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->where('store_id', $selectedStoreId);
            })
                ->when($dateRange !== 'all', $dateFilter)
                ->sum('net_total');

            // Get purchase due
            $totalPurchaseDue = Purchase::when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->where('store_id', $selectedStoreId);
            })
                ->when($dateRange !== 'all', $dateFilter)
                ->sum('payment_due');

            // Get Purchase Returns
            $totalPurchaseReturns = PurchaseReturn::when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->whereHas('purchase', function ($q) use ($selectedStoreId) {
                    $q->where('store_id', $selectedStoreId);
                });
            })
                ->when($dateRange !== 'all', $dateFilter)
                ->sum('total_return_amount');

            $statistics[] = [
                'title' => 'Total Purchases',
                'count' => number_format($totalPurchases, 2),
                'icon' => 'bx-cart',
                'color' => 'bg-gradient-kyoto'
            ];
            $statistics[] = [
                'title' => 'Total Purchase Due',
                'count' => number_format($totalPurchaseDue, 2),
                'icon' => 'bx-money',
                'color' => 'bg-gradient-burning'
            ];
            $statistics[] = [
                'title' => 'Total Purchase Returns',
                'count' => number_format($totalPurchaseReturns, 2),
                'icon' => 'bx-undo',
                'color' => 'bg-gradient-ohhappiness'
            ];
        }

        // Get Sales (visible to all roles)
        $totalSales = Sell::with('payments')
            ->when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->where('store_id', $selectedStoreId);
            })
            ->when($dateRange !== 'all', $dateFilter)
            ->sum('net_total');

        // Get Sales Due
        $totalSalesDue = Sell::with('payments')
            ->when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->where('store_id', $selectedStoreId);
            })
            ->when($dateRange !== 'all', $dateFilter)
            ->sum('payment_due');

        // Get Expense
        $totalExpense = Expense::when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->where('store_id', $selectedStoreId);
            })
            ->when($dateRange !== 'all', $dateFilter)
            ->sum('total_amount');

        // Get Bank Withdrawals
        $bankWithdrawals = AccountTransaction::when($selectedStoreId, function ($query) use ($selectedStoreId) {
                return $query->where('store_id', $selectedStoreId);
            })
            ->when($dateRange !== 'all', $dateFilter)
            ->where('transaction_type', 'withdrawal')
            ->sum('amount');

        // Calculate cash components
        $totalCashReceived = $totalSales - $totalSalesDue;
        $totalAfterExpenses = $totalCashReceived - $totalExpense;
        $totalCashInHand = $totalAfterExpenses - $bankWithdrawals;

        // Update current balance in the database
        DB::table('accounts')
            ->where('store_id', $selectedStoreId)
            ->update(['current_balance' => $totalCashInHand]);

        // Add remaining stats (visible to all roles)
        $statistics = array_merge($statistics, [
            [
                'title' => 'Total Sales',
                'count' => number_format($totalSales, 2),
                'icon' => 'bx-cart',
                'color' => 'bg-gradient-kyoto'
            ],
            [
                'title' => 'Total Sales Due',
                'count' => number_format($totalSalesDue, 2),
                'icon' => 'bx-money',
                'color' => 'bg-gradient-burning'
            ],
            [
                'title' => 'Total Expense',
                'count' => number_format($totalExpense, 2),
                'icon' => 'bx-money',
                'color' => 'bg-gradient-burning'
            ],
            [
                'title' => 'Total Cash In Hand',
                'count' => number_format($totalCashInHand, 2),
                'icon' => 'bx-line-chart',
                'color' => 'bg-gradient-kyoto'
            ],
            [
                'title' => 'Total Bank Withdrawals',
                'count' => number_format($bankWithdrawals, 2),
                'icon' => 'bx-line-chart',
                'color' => 'bg-gradient-kyoto'
            ],
        ]);

        return $statistics;
    } catch (Exception $e) {
        Log::error('Dashboard Statistics Error: ' . $e->getMessage());
        return [];
    }
}
 
}