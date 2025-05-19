<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\BankAccount;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WithdrawalService
{
	/**
	 * Process a withdrawal from cash to bank account
	 *
	 * @param int         $storeId
	 * @param int         $bankAccountId
	 * @param float       $amount
	 * @param string|null $notes
	 * @param int         $userId
	 * @return Withdrawal
	 */
	public function processWithdrawal(int $storeId, int $bankAccountId, float $amount, ?string $notes = null, int $userId): Withdrawal {
		return DB::transaction (function () use ($storeId, $bankAccountId, $amount, $notes, $userId) {
			// Get account for this store
			$account = Account::where ('store_id', $storeId)->firstOrFail ();

			// Verify sufficient funds
			if ($account->current_balance < $amount) {
				throw new \Exception('Insufficient funds in cash account');
			}

			// Get bank account
			$bankAccount = BankAccount::findOrFail ($bankAccountId);

			// Update cash account balance
			$account->current_balance -= $amount;
			$account->save ();

			// Update bank account balance
			$bankAccount->current_balance += $amount;
			$bankAccount->save ();

			// Create withdrawal record
			$withdrawal = new Withdrawal();
			$withdrawal->store_id = $storeId;
			$withdrawal->account_id = $account->id;
			$withdrawal->bank_account_id = $bankAccountId;
			$withdrawal->reference_number = 'WDR-' . Str::uuid ()->toString ();
			$withdrawal->amount = $amount;
			$withdrawal->withdrawal_date = Carbon::today ();
			$withdrawal->notes = $notes;
			$withdrawal->created_by_id = $userId;
			$withdrawal->save ();

			// Create transaction record for withdrawal
			$transaction = new AccountTransaction();
			$transaction->transaction_id = 'WDR-' . $withdrawal->reference_number;
			$transaction->store_id = $storeId;
			$transaction->account_id = $account->id;
			$transaction->transaction_type = 'withdrawal';
			$transaction->transaction_source = 'Bank Transfer';
			$transaction->amount = $amount;
			$transaction->description = "Withdrawal to {$bankAccount->bank_name} account {$bankAccount->account_number}";
			$transaction->transaction_date = Carbon::today ();
			$transaction->created_by_id = $userId;
			$transaction->save ();

			// Update daily cash report
			$saleService = new SaleTransactionService();
			$saleService->updateDailyCashReport ($storeId, $account->id, $userId);

			return $withdrawal;
		});
	}

	/**
	 * Get withdrawal history for a store
	 *
	 * @param int         $storeId
	 * @param string|null $startDate
	 * @param string|null $endDate
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getWithdrawalHistory(int $storeId, ?string $startDate = null, ?string $endDate = null) {
		$query = Withdrawal::where ('store_id', $storeId);

		if ($startDate) {
			$query->where ('withdrawal_date', '>=', $startDate);
		}

		if ($endDate) {
			$query->where ('withdrawal_date', '<=', $endDate);
		}

		return $query->orderBy ('withdrawal_date', 'desc')->get ();
	}
}