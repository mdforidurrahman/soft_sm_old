<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StoreController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (request()->ajax()) {

            if (Auth::User()->hasRole('admin')) {
                $stores = Store::latest()->get();
            } else {
                $stores = Auth::User()->stores;
            }

            return DataTables::of($stores)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    return view('components.status-toggle', [
                        'id' => $row->id,
                        'model' => 'store',
                        'status' => $row->status
                    ])->render();
                })
                ->addColumn('action', function ($row) {
                    return view('components.action-buttons', [
                        'id' => $row->id,
                        'model' => 'project',
                        'editModal' => 'editModal',
                        'editModalRoute' => 'stores.edit',
                        'deleteRoute' => 'stores.destroy',
                    ])->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.stores.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStoreRequest $request)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Create the store
            $store = Store::create([
                'name' => $request->name,
                'address' => $request->address,
                'status' => $request->status,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);

            // Retrieve the current user's role
            $userRole = Auth::user()->roles->first()->name; // Assuming a user has one primary role

            // Assign the store to the current user with the role
            Auth::user()->stores()->attach($store->id, [
                'role' => $userRole
            ]);

            // Commit the transaction
            DB::commit();

            return $this->success(['id' => $store->id], 'Store created successfully');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            return $this->error('Something Went Wrong : ', $e->getMessage(), 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Store $store) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $store = Store::findOrFail($id);
        return response()->json($store);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreRequest $request, $id)
    {
        try {
            $store = Store::findOrFail($id);

            $store->update([
                'name' => $request->name,
                'address' => $request->address,
                'updated_by' => Auth::id()
            ]);

            return $this->success(['id' => $store->id], 'Store Updated successfully');
        } catch (\Exception $exception) {
            return $this->error('Something went wrong' . $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */


    public function destroy(Request $request, $id)
    {
        try {
            $store = Store::findOrFail($id);

            // Start a database transaction
            DB::beginTransaction();

            try {
                // Delete all related records
                $store->products()->delete();
                $store->Expenses()->delete();
                $store->purchases()->delete();
                $store->sells()->delete();

                // Finally delete the store
                $store->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Store and all related records deleted successfully'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
}
