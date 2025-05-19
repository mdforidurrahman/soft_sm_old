<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Store;
use App\Models\Withdrawal;
use App\Services\SaleTransactionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class WithdrawalController extends Controller
{
    protected $transactionService;

    public function __construct(SaleTransactionService $transactionService) {
        $this->transactionService = $transactionService;
    }

    /**
     * Display a listing of withdrawals
     */
    public function index(Request $request) {
        $bankAccounts = BankAccount::get();
        
        if (Auth::User()->hasRole('admin')) {
            $stores = Store::with('account')->latest()->get();
        } else {
            $stores = Auth::User()->stores;
        }

        if (request()->ajax()) {
            $data = Withdrawal::with(['store','account','bankAccount'])->latest();
            
            // Filter withdrawals for non-admin users
            if (!Auth::User()->hasRole('admin')) {
                $data->whereIn('store_id', Auth::User()->stores->pluck('id'));
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('store', function ($row) {
                    return $row->store->name;
                })
                ->addColumn('bankAccount', function ($row) {
                    return $row->bankAccount->bank_name;
                })
                ->addColumn('status', function ($row) {
                    return view('components.status-toggle', [
                        'id' => $row->id,
                        'model' => 'withdrawal',
                        'status' => $row->status
                    ])->render();
                })
                ->addColumn('action', function ($row) {
                    // Hide action buttons if user doesn't have permission
                    if (!Auth::User()->hasRole('admin') && 
                        !Auth::User()->stores->contains('id', $row->store_id)) {
                        return '';
                    }
                    
                    return view('components.action-buttons', [
                        'id' => $row->id,
                        'model' => 'withdrawal',
                        'editModal' => 'editModal',
                        'editModalRoute' => 'withdrawals.edit',
                        'deleteRoute' => 'withdrawals.destroy',
                    ])->render();
                })
                ->rawColumns(['action', 'status', 'invoice'])
                ->make(true);
        }

        return view('admin.withdrawal.index', compact('stores', 'bankAccounts'));
    }

    /**
     * Store a new withdrawal
     */
    public function store(Request $request) {
        try {
            // Authorization check for non-admin users
            if (!Auth::User()->hasRole('admin') && 
                !Auth::User()->stores->contains('id', $request->store_id)) {
                abort(403, 'Unauthorized action.');
            }

            DB::beginTransaction();
            // Validate request
            $validatedData = $request->validate([
                'store_id' => 'required|integer|exists:stores,id',
                'amount' => 'required|numeric|min:0.01',
                'bank_account_id' => 'required|integer|exists:bank_accounts,id',
                'description' => 'nullable|string|max:255',
            ]);

            $account = Account::where('store_id', $request->store_id)->first();

            if ($account->current_balance < $request->amount) {
                return response()->json([
                    'error' => 'Insufficient balance for withdrawal',
                    'balance' => $account->current_balance
                ], 400);
            }
            
            // Record withdrawal transaction
            $transaction = $this->transactionService->recordWithdrawal(
                $request->store_id,
                $request->amount,
                $request->bank_account_id,
                'Withdrawal from ' . Auth::user()->name,
                Auth::id()
            );
            
            DB::commit();
            return response()->json([
                'message' => 'Withdrawal recorded successfully',
                'withdrawal_id' => $transaction->reference_id,
                'transaction_id' => $transaction->transaction_id
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified withdrawal
     */
    public function show($id) {
        $withdrawal = Withdrawal::with('bankAccount')->findOrFail($id);
        
        // Authorization check
        if (!Auth::User()->hasRole('admin') && 
            !Auth::User()->stores->contains('id', $withdrawal->store_id)) {
            abort(403, 'Unauthorized action.');
        }

        // Get associated transaction
        $transaction = $this->transactionService->getTransactionByReference(
            $withdrawal->id,
            'withdrawal'
        );

        return response()->json([
            'withdrawal' => $withdrawal,
            'transaction' => $transaction
        ]);
    }

    public function edit($id) {
        $withdrawal = Withdrawal::with(['store', 'store.account', 'bankAccount'])->findOrFail($id);
        
        // Authorization check
        if (!Auth::User()->hasRole('admin') && 
            !Auth::User()->stores->contains('id', $withdrawal->store_id)) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json([
            'id' => $withdrawal->id,
            'store_id' => $withdrawal->store_id,
            'bank_account_id' => $withdrawal->bank_account_id,
            'amount' => $withdrawal->amount,
            'notes' => $withdrawal->notes,
            'store_balance' => $withdrawal->store->account->current_balance ?? 0,
            'bank_account' => $withdrawal->bankAccount ? [
                'id' => $withdrawal->bankAccount->id,
                'bank_name' => $withdrawal->bankAccount->bank_name,
                'account_number' => $withdrawal->bankAccount->account_number
            ] : null
        ]);
    }

    /**
     * Update the specified withdrawal
     */
    public function update(Request $request, $id) {
        try {
            $withdrawal = Withdrawal::findOrFail($id);
            
            // Authorization check
            if (!Auth::User()->hasRole('admin') && 
                !Auth::User()->stores->contains('id', $withdrawal->store_id)) {
                abort(403, 'Unauthorized action.');
            }

            DB::beginTransaction();

            // Validate request
            $validatedData = $request->validate([
                'amount' => 'sometimes|required|numeric|min:0.01',
                'bank_account_id' => 'sometimes|required|integer|exists:bank_accounts,id',
                'description' => 'nullable|string|max:255',
            ]);

            // If amount is increasing, check balance
            if (isset($request->amount) && $request->amount > $withdrawal->amount) {
                $amountDifference = $request->amount - $withdrawal->amount;
                $account = Account::where('store_id', $withdrawal->store_id)->first();

                if (!$account || $account->current_balance < $amountDifference) {
                    return response()->json([
                        'error' => 'Insufficient balance for increased withdrawal amount'
                    ], 400);
                }
            }

            // Update transaction first
            $transaction = $this->transactionService->updateTransaction(
                $withdrawal->id,
                'withdrawal',
                $request->amount ?? $withdrawal->amount,
                null, // Source typically stays as 'Bank Transfer'
                $request->description ?? $withdrawal->notes,
                Auth::id()
            );

            if (!$transaction) {
                return response()->json([
                    'error' => 'Could not update withdrawal transaction'
                ], 500);
            }

            // Update withdrawal record
            if (isset($request->amount)) {
                $withdrawal->amount = $request->amount;
            }

            if (isset($request->bank_account_id)) {
                $withdrawal->bank_account_id = $request->bank_account_id;
            }

            if (isset($request->description)) {
                $withdrawal->notes = $request->description;
            }

            $withdrawal->save();

            $transaction = $this->transactionService->updateWithdrawal(
                $withdrawal->id,
                $request->amount,
                $request->bank_account_id,
                'Withdrawal from ' . Auth::user()->name,
                Auth::id()
            );
            
            DB::commit();
            return response()->json([
                'message' => 'Withdrawal updated successfully',
                'withdrawal' => $withdrawal,
                'transaction' => $transaction
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


/**
 * Remove the specified withdrawal
 */
public function destroy($id) {
    try {
        DB::beginTransaction();
        
        // Find withdrawal
        $withdrawal = Withdrawal::findOrFail($id);
        
        // Authorization check
        if (!Auth::User()->hasRole('admin') && 
            !Auth::User()->stores->contains('id', $withdrawal->store_id)) {
            abort(403, 'Unauthorized action.');
        }

        // Delete transaction first
        $transactionDeleted = $this->transactionService->deleteTransaction(
            $withdrawal->id,
            'withdrawal',
            Auth::id()
        );

        if (!$transactionDeleted) {
            throw new Exception('Could not delete withdrawal transaction');
        }

        // Delete withdrawal record
        $withdrawal->delete();

        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Withdrawal deleted successfully'
        ]);

    } catch (Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Get available bank accounts for withdrawal
     */
    public function getBankAccounts($store_id) {
        // Authorization check for non-admin users
        if (!Auth::User()->hasRole('admin') && 
            !Auth::User()->stores->contains('id', $store_id)) {
            abort(403, 'Unauthorized action.');
        }

        $bankAccounts = BankAccount::where('store_id', $store_id)
            ->orderBy('bank_name')
            ->get(['id', 'bank_name', 'account_number', 'current_balance']);

        return response()->json([
            'bank_accounts' => $bankAccounts
        ]);
    }
}