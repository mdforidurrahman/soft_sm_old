<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Models\BusinessLocation;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Http\Requests\StoreBusinessLocationRequest;
use App\Http\Requests\UpdateBusinessLocationRequest;

class BusinessLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::User()->hasRole('admin')) {
            $storeName = Store::where('status', 1)->latest()->get();
        } else {
            $storeName = Auth::User()->stores;
        }

        if (request()->ajax()) {
            $projects = BusinessLocation::latest();
            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('store_id', function ($row) {
                    return $row['store']['name'];
                })
                ->addColumn('status', function ($row) {
                    return view('components.status-toggle', [
                        'id' => $row->id,
                        'model' => 'BusinessLocation',
                        'status' => $row->status
                    ])->render();
                })
                ->addColumn('action', function ($row) {
                    return view('components.action-buttons', [
                        'id' => $row->id,
                        'model' => 'BusinessLocation',
                        'editModal' => 'editModal',
                        'editModalRoute' => 'business-location.edit',
                        'deleteRoute' => 'business-location.destroy',
                    ])->render();
                })
                ->rawColumns(['action', 'store_id', 'status'])
                ->make(true);
        }

        return view('admin.businessLocation.index', compact('storeName'));
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
    public function store(StoreBusinessLocationRequest $request)
    {
        try {
            // Data save
            $businessLocation = new BusinessLocation();
            $businessLocation->store_id = $request->input('store_id');
            $businessLocation->landmark = $request->landmark;
            $businessLocation->city = $request->city;
            $businessLocation->zip_code = $request->zip_code;
            $businessLocation->state = $request->state;
            $businessLocation->country = $request->country;
            $businessLocation->status = $request->status;

            // Authenticated user ID as created_by and updated_by
            $businessLocation->created_by = auth()->id();
            $businessLocation->updated_by = auth()->id();

            $businessLocation->save();


            return $this->success(['id' => $businessLocation->id], 'Business Test Location created successfully');
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BusinessLocation $businessLocation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = BusinessLocation::with('store')->findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $businessLocation = BusinessLocation::findOrFail($id);
            $request->validate([
                'editstore_id' => 'required|exists:stores,id|unique:business_locations,store_id,' . $businessLocation->id,
                'editlandmark' => 'required|string|max:255',
                'editcity' => 'required|string|max:100',
                'editzip_code' => 'required|string|max:20',
                'editstate' => 'required|string|max:100',
                'editcountry' => 'required|string|max:100',
                'editstatus' => 'required|boolean',
            ]);

            // Data save
            $businessLocation->store_id = $request->input('editstore_id');
            $businessLocation->landmark = $request->input('editlandmark');
            $businessLocation->city = $request->input('editcity');
            $businessLocation->zip_code = $request->input('editzip_code');
            $businessLocation->state = $request->input('editstate');
            $businessLocation->country = $request->input('editcountry');
            $businessLocation->status = $request->input('editstatus');

            // Authenticated user ID as created_by and updated_by
            $businessLocation->created_by = auth()->id();
            $businessLocation->updated_by = auth()->id();

            $businessLocation->save();

            return $this->success(['id' => $businessLocation->id], 'Business Location updated successfully');
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $data = BusinessLocation::findOrFail($id);

            $data->delete();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Business Location deleted successfully']);
            }

            return $this->success('Business  Location Deleted successfully');
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }
}
