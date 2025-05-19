<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $guarded = [];

	public function bankAccount() {
		return $this->belongsTo(BankAccount::class);
	}

	public function store() {
		return $this->belongsTo(Store::class);
	}

	public function account() {
		return $this->belongsTo(Account::class);
	}
}
