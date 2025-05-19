<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
		if (Auth::User()->hasRole('admin')) {
			$stores = Store::latest()->get();
		} else {
			$stores = Auth::User()->stores;
		}
		if (request()->ajax()) {
			$data = BankAccount::with('store')->latest();
			return DataTables::of($data)
				->addIndexColumn()
				->addColumn('store', function ($row) {
					return $row->store->name;
				})
				->addColumn('status', function ($row) {
					return view('components.status-toggle', [
						'id' => $row->id,
						'model' => 'bankAccount',
						'status' => $row->status
					])->render();
				})
				->addColumn('action', function ($row) {
					return view('components.action-buttons', [
						'id' => $row->id,
						'model' => 'bankAccount',
						'editModal' => 'editModal',
						'editModalRoute' => 'banks.edit',
						'deleteRoute' => 'banks.destroy',
					])->render();
				})
				->rawColumns(['action', 'status', 'invoice'])
				->make(true);
		}

		return view('admin.bank.index', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
