<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{


    public function index(Request $request)
    {
        $roles = Role::all();
        $authUser = auth()->user();

        if ($authUser->hasRole('admin')) {
            $stores = Store::where('status',1)->latest()->get();
        } else {
            $stores = $authUser->stores;
        }


        if ($request->ajax()) {
            $data = User::latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('role', function ($row) {
                    return view('components.role-icon', ['userRole' => $row]);
                })
                ->addColumn('status', function ($row) {
                    return view('components.status-toggle', [
                        'id' => $row->id,
                        'model' => 'user',
                        'status' => $row->status
                    ])->render();
                })
                ->addColumn('stores', function ($row) {
                    return view('components.store-list', ['stores' => $row->stores])->render();
                })
                ->addColumn('store_assignment', function ($row) {
                    return view('components.store-assignment-button', [
                        'id' => $row->id,
                        'modalTarget' => '#assignStoresModal'
                    ])->render();
                })
                ->addColumn('action', function ($row) {
                    return view('components.action-buttons', [
                        'id' => $row->id,
                        'model' => 'user',
                        'editModal' => 'editModal',
                        'editModalRoute' => 'user.edit',
                        'deleteRoute' => 'user.destroy',
                    ])->render();
                })
                ->rawColumns(['action', 'status', 'stores', 'store_assignment'])
                ->make(true);
        }

        return view('admin.user.index', compact('roles', 'stores'));
    }


    public function assignStores(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'stores' => 'required|array',
            'stores.*' => 'exists:stores,id',
            'roles' => 'sometimes|array',
        ]);

        // Sync stores with optional roles
        $storesToSync = [];
        foreach ($validatedData['stores'] as $index => $storeId) {
            $role = $validatedData['roles'][$index] ?? 'staff';
            $storesToSync[$storeId] = ['role' => $role];
        }

        $user->stores()->sync($storesToSync);

        return response()->json([
            'message' => 'Stores assigned successfully',
            'assigned_stores' => $user->stores
        ]);
    }

    public function getUserStores(User $user)
    {
        return response()->json([
            'assigned_stores' => $user->stores
        ]);
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => bcrypt($request->password)
            ]);

            if ($request->roles) {
                foreach ($request->roles as $role) {
                    $user->addRole($role);
                }
            }

            // Upload Photo

            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $name_gen = hexdec(uniqid()) . '.' . $photo->getClientOriginalExtension();
                Image::make($photo)->resize(300, 300)->save('upload/user/' . $name_gen);
                $save_url = 'upload/user/' . $name_gen;
                $user->update(['photo' => $save_url]);
            }

            return redirect()->back()->with('success', 'user created successfully');
        } catch (\Throwable $th) {

            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Factory|View|Application|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = User::findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        try {
            $user = User::findOrFail($id);
            // Log::info('Fetched Users:', $request->photo);
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'photo' => 'nullable|image|max:5144',
            ]);

            $user->name = $request->input('name'); 
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');
            $user->address = $request->input('address');
            //$user->password = Hash::make($request->input('password'));
            $user->save();

            if ($request->has('roles')) {
                $user->removeRoles();
                foreach ($request->roles as $roleName) {
                    $role = Role::where('name', $roleName)->first();
                    if ($role) {
                        $user->addRole($role);
                    }
                }
            }

            // if ($request->password) {
            //     $user->update(['password' => bcrypt($request->password)]);
            // }


            // Upload Photo
            if ($request->hasFile('photo')) {
                // Remove old image if exists
                if ($user->photo) {
                    $old_image = public_path($user->photo);
                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }
                }

                $photo = $request->file('photo');
                $name_gen = hexdec(uniqid()) . '.' . $photo->getClientOriginalExtension();
                Image::make($photo)->resize(300, 300)->save('upload/user/' . $name_gen);
                $save_url = 'upload/user/' . $name_gen;

                $user->update([
                    'photo' => $save_url
                ]);
            }


            // Send welcome email
            //  Mail::to($user->email)->send(new WelcomeUserMail($user));

            flash()->success('User updated successfully!');
            return response()->json(['message' => 'Store updated successfully', 'store' => $user]);
        } catch (\Throwable $exception) {
            flash()->error($exception->getMessage());
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            // Remove user's roles
            $user->syncRoles([]);
            // Delete user's photo if it exists
            if ($user->photo) {
                $old_image = public_path($user->photo);
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }

            $user->delete();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'User deleted successfully']);
            }

            flash('success', 'User deleted successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'User not deleted successfully: ' . $e->getMessage()], 500);
            }

            flash('error', 'User not deleted successfully: ' . $e->getMessage());
            return redirect()->back();
        }
    }

}
