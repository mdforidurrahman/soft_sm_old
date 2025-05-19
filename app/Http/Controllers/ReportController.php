<?php

namespace App\Http\Controllers;
use App\Models\Sell;
use Carbon\Carbon;
use PDF;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\User;
use App\Models\Store;
use App\Models\Expense;
use Yajra\DataTables\DataTables;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\ExpenseCategory;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = Expense::with(['user', 'contact', 'store', 'expenseCategory'])->latest()->get();

        // Fetch Filters
        $stores = Store::latest()->get();
        $users = User::latest()->get();
        $contacts = Contact::latest()->get();
        $categories = ExpenseCategory::latest()->get();
        $role = auth()->user()?->roles()?->first()?->name ?? 'admin';

        // Day-Wise Report
        $dayWise = Expense::selectRaw('DATE(expense_date) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Customer-Wise Report
        $customerWise = Expense::with('contact')
            ->selectRaw('expense_for_contact, SUM(total_amount) as total')
            ->groupBy('expense_for_contact')
            ->get();

        // Total Calculations
        $totalAmount = Expense::sum('total_amount');
//        $totalRefunded = Expense::where('is_refund', true)->sum('total_amount');

        // JSON Data for Charts
        $dayWiseChart = $dayWise->map(fn($day) => ['label' => $day->date, 'value' => $day->total])->values();
        $customerWiseChart = $customerWise->map(fn($customer) => [
            'label' => $customer->contact?->name ?? 'Unknown',
            'value' => $customer->total,
        ])->values();

        $monthlyExpenses = Expense::selectRaw('MONTH(expense_date) as month, YEAR(expense_date) as year, SUM(total_amount) as total')
            ->groupBy('year', 'month')
            ->orderByRaw('year DESC, month DESC')
            ->get();

        $monthlyExpenseChart = $monthlyExpenses->map(fn($month) => [
            'label' => date('F Y', mktime(0, 0, 0, $month->month, 1, $month->year)),
            'value' => $month->total,
        ]);

        return view('admin.report.expensereport', compact(
            'stores',
            'users',
            'contacts',
            'categories',
            'dayWise',
            'customerWise',
            'totalAmount',
            'dayWiseChart',
            'customerWiseChart',
            'monthlyExpenseChart'
        ));
    }





    public function sells(Request $request)
    {
        // Get all sales
        $sales = Sell::all();

        // Group sales by date
        $salesByDate = Sell::selectRaw('DATE(sell_date) as date, SUM(net_total) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Filter sales for the last 30 days
        $salesLast30Days = Sell::where('sell_date', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(sell_date) as date, SUM(net_total) as total')
            ->groupBy('date')
            ->get();

        // Calculate total sales, total tax, and total discount
        $totalSales = Sell::sum('net_total');
        $totalTax = Sell::sum('tax_amount');
        $totalDiscount = Sell::sum('discount_amount');

        // Prepare data for charts
        $salesDates = $salesLast30Days->pluck('date')->toArray();
        $salesTotals = $salesLast30Days->pluck('total')->toArray();

        // Return data to the view (Blade file)
        return view('admin.report.sells-Report', [
            'sales' => $sales,
            'salesByDate' => $salesByDate,
            'salesLast30Days' => $salesLast30Days,
            'totalSales' => $totalSales,
            'totalTax' => $totalTax,
            'totalDiscount' => $totalDiscount,
            'salesDates' => $salesDates,
            'salesTotals' => $salesTotals
        ]);
    }





    public function downloadInvoice($id)
    {
        $expense = Expense::with(['store', 'user', 'contact'])->findOrFail($id);

        // Generate PDF using a view file, e.g., 'invoices.invoice-pdf'
        $pdf = PDF::loadView('invoices.invoice-pdf', compact('expense'));

        return $pdf->download("invoice_{$id}.pdf");
    }

    /**
     * Show the invoice in a modal.
     */
    public function showInvoice($id)
    {
        // Ensure you are retrieving a model instance with its relationships loaded
        $expense = Expense::with(['store', 'user', 'contact', 'expenseCategory'])->findOrFail($id);

        // Return the rendered view
        return view('admin.expense.invoice-modal', compact('expense'))->render();
    }




    public function show($id)
    {
        return $id;
        $expense = Expense::findOrFail($id);
        return response()->json($expense);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {


    }
    public function edit($id)
    {
        $expense = Expense::with(['store', 'user', 'contact', 'expenseCategory'])->findOrFail($id);
        return response()->json($expense);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request)
    {

    }


    public function saveData(Request $request)
    {
        \Log::info('Store method called');

        // Validate the request data
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'expense_category_id' => 'required|exists:expense_category,id',
            'reference_no' => 'nullable|string|unique:expenses',
            'expense_date' => 'required|date',
            'expense_for_id' => 'nullable|exists:users,id',
            'expense_for_contact' => 'nullable|exists:contacts,id',
            'document' => 'nullable|file|max:5120',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
            'status' => 'boolean'
        ]);

        // Handle default values
        $isRefund = $request->has('is_refund') ? (bool) $request->is_refund : false;
        $status = $request->has('status') ? (bool) $request->status : false;

        // Auto-generate the reference_no if not provided
        $referenceNo = $request->reference_no ?: 'EXP-' . strtoupper(uniqid());

        // Handle document upload
        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        }

        // Create the expense record
        Expense::create([
            'store_id' => $request->store_id,
            'expense_category_id' => $request->expense_category_id,
            'reference_no' => $referenceNo,
            'expense_date' => $request->expense_date,
            'expense_for_id' => $request->expense_for_id,
            'expense_for_contact' => $request->expense_for_contact,
            'document' => $documentPath,
            'total_amount' => $request->total_amount,
            'status' => $status
        ]);

        return response()->json(['success' => 'Expense added successfully!']);
    }





    public function updateData(Request $request, $id)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'reference_no' => 'nullable|string|unique:expenses,reference_no,' . $id,
            'expense_date' => 'required|date',
            'expense_for_id' => 'nullable|exists:users,id',
            'expense_for_contact' => 'nullable|exists:contacts,id',
            'document' => 'nullable|file|max:5120',
            'total_amount' => 'required|numeric',
            'status' => 'boolean'
        ]);

        $expense = Expense::findOrFail($id);
        $documentPath = $expense->document;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        }

        $expense->update([
            'store_id' => $request->store_id,
            'expense_category_id' => $request->expense_category_id,
            'reference_no' => $request->reference_no ?? $expense->reference_no,
            'expense_date' => $request->expense_date,
            'expense_for_id' => $request->expense_for_id,
            'expense_for_contact' => $request->expense_for_contact,
            'document' => $documentPath,
            'total_amount' => $request->total_amount,
            'status' => $request->status ?? false
        ]);

        return response()->json(['success' => 'Expense updated successfully!']);
    }



    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */

        // Display the expense categories view
      // Add a new method for handling the expense category


}
