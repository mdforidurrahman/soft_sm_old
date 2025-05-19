<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreUserController extends Controller
{
    /**
     * Add a user to a specific store
     */
    public function addUserToStore(Request $request, Store $store)
    {
        // Validate the request
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'sometimes|in:staff,manager,admin', // adjust roles as needed
        ]);

        try {
            // Check if the user is already assigned to this store
            $existingAssignment = DB::table('user_stores')
                ->where('user_id', $validatedData['user_id'])
                ->where('store_id', $store->id)
                ->first();

            if ($existingAssignment) {
                return response()->json([
                    'message' => 'User is already assigned to this store'
                ], 400);
            }

            // Attach the user to the store
            $store->users()->attach($validatedData['user_id'], [
                'role' => $validatedData['role'] ?? 'staff'
            ]);

            return response()->json([
                'message' => 'User successfully added to the store'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to add user to store',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a user from a specific store
     */
    public function removeUserFromStore(Store $store, User $user)
    {
        try {
            $store->users()->detach($user->id);

            return response()->json([
                'message' => 'User successfully removed from the store'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove user from store',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}