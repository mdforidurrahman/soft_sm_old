<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\BankAccount;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AccountTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        if (Auth::User()->hasRole('admin')) {
            $stores = Store::latest()->get();
        } else {
            $stores = Auth::User()->stores;
        }

        if (request()->ajax()) {
            $data = AccountTransaction::with(['store', 'account', 'createdBy'])
                ->latest();

            // If user is not admin, filter transactions by their stores only
            if (!Auth::User()->hasRole('admin')) {
                $data->whereIn('store_id', Auth::User()->stores->pluck('id'));
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('store', function ($row) {
                    return $row->store->name;
                })
                ->addColumn('transaction_type', function ($row) {
                    return ucfirst($row->transaction_type);
                })
                ->addColumn('amount', function ($row) {
                    return number_format($row->amount, 2);
                })
                ->addColumn('created_by', function ($row) {
                    return $row->createdBy->name;
                })
                ->addColumn('transaction_date', function ($row) {
                    return $row->transaction_date;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.account-transactions.index', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        try {
            $request->validate([
                'store_id' => 'required|exists:stores,id',
                'bank_name' => 'required|string|max:255',
                'account_holder_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:255|unique:bank_accounts,account_number',
                'current_balance' => 'required|numeric|min:0',
            ]);

            // Authorization check for non-admin users
            if (!Auth::User()->hasRole('admin') && 
                !Auth::User()->stores->contains('id', $request->store_id)) {
                abort(403, 'Unauthorized action.');
            }

            DB::beginTransaction();

            $data = BankAccount::create([
                'store_id' => $request->store_id,
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'current_balance' => $request->current_balance,
                'created_by' => Auth::id()
            ]);
            
            DB::commit();

            return $this->success(['id' => $data->id], 'Bank Account created successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->error('Something Went Wrong : ', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        $transaction = AccountTransaction::with(['store', 'account', 'createdBy'])->findOrFail($id);
        
        // Authorization check
        if (!Auth::User()->hasRole('admin') && 
            !Auth::User()->stores->contains('id', $transaction->store_id)) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json($transaction);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        $bankAccount = BankAccount::findOrFail($id);
        
        // Authorization check
        if (!Auth::User()->hasRole('admin') && 
            !Auth::User()->stores->contains('id', $bankAccount->store_id)) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json($bankAccount);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        try {
            $bank = BankAccount::findOrFail($id);
            
            // Authorization check
            if (!Auth::User()->hasRole('admin') && 
                !Auth::User()->stores->contains('id', $bank->store_id)) {
                abort(403, 'Unauthorized action.');
            }

            $request->validate([
                'store_id' => 'required|exists:stores,id',
                'bank_name' => 'required|string|max:255',
                'account_holder_name' => 'required|string|max:255',
                'account_number' => 'required|string|max:255|unique:bank_accounts,account_number,' . $id,
                'current_balance' => 'required|numeric|min:0',
            ]);

            // Additional check if changing store_id
            if (!Auth::User()->hasRole('admin') && 
                $request->store_id != $bank->store_id && 
                !Auth::User()->stores->contains('id', $request->store_id)) {
                abort(403, 'Unauthorized action.');
            }

            $bank->update($request->all());
            DB::commit();

            return $this->success(['id' => $bank->id], 'Bank Updated successfully');
        } catch (\Exception $exception) {
            return $this->error('Something went wrong' . $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        try {
            $bank = BankAccount::findOrFail($id);
            
            // Authorization check
            if (!Auth::User()->hasRole('admin') && 
                !Auth::User()->stores->contains('id', $bank->store_id)) {
                abort(403, 'Unauthorized action.');
            }

            DB::beginTransaction();
            $bank->delete();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Bank deleted successfully'
            ]);
        } catch (\Exception $exception) {
            return $this->error('Something went wrong' . $exception->getMessage());
        }
    }
  
  
  //accountTransactionsSummary
public function accountTransactionsSummary(Request $request)
{
    $transactions = AccountTransaction::selectRaw('
            DATE(transaction_date) as date,
            SUM(CASE WHEN transaction_type = "income" THEN amount ELSE 0 END) as income,
            SUM(CASE WHEN transaction_type = "expense" THEN amount ELSE 0 END) as expense,
            SUM(CASE WHEN transaction_type = "withdrawal" THEN amount ELSE 0 END) as withdrawal,
            SUM(CASE WHEN transaction_type = "adjustment" THEN amount ELSE 0 END) as adjustment
        ')
        ->groupBy('date')
        ->orderBy('date', 'desc');

    if ($request->ajax()) {
        return datatables()->of($transactions)
            ->addIndexColumn()
            ->addColumn('calculation', function($row) {
                $total = $row->income - ($row->expense + $row->withdrawal + $row->adjustment);
                return $total;
            })
            ->rawColumns(['calculation'])
            ->make(true);
    }

    return view('admin.account-transactions.transactions-summary');
}
  
}