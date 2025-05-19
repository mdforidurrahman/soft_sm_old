<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Throwable;

class AdminController extends Controller
{
    public function toggleUserStatus($id) {
        try {
            $user = User::findOrFail($id);
            $user->status = $user->status == 1 ? 0 : 1;
            $user->save();

            $notification = [
                'message' => 'Status updated successfully',
                'alert-type' => 'success',
            ];

            return redirect()->back()->with($notification);
        } catch (\Throwable $th) {
            $notification = [
                'message' => $th->getMessage(),
                'alert-type' => 'error',
            ];

            return redirect()->back()->with($notification);
        }
    }

    public function toggleStatus($model, $id) {
        try {
            $model = 'App\\Models\\' . ucfirst($model);

            if (!class_exists($model)) {
                return response()->json(['message' => 'Model not found.'], 404);
            }
            $item = $model::find($id);

            if (!$item) {
                return response()->json(['message' => 'Item not found.'], 404);
            }

            $item->status = $item->status === 1 ? 0 : 1;
            $item->save();

            return response()->json(['message' => 'Status updated successfully.', 'status' => $item->status]);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }


    public function getProducts() {
        $products = Product::whereStatus(1)
            ->where('stock', '>', 0)
            ->orderBy('created_at', 'desc')->get();

        return response()->json($products);
    }
}
