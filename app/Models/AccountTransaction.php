<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
	protected $guarded = [];

	public function store() {
		return $this->belongsTo(Store::class);
	}

	public function account() {
		return $this->belongsTo(Account::class);
	}

	public function createdBy() {
		return $this->belongsTo(User::class, 'created_by_id');
	}
}
