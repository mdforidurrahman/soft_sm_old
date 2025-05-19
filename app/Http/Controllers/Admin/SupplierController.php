<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function search(Request $request) {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);

        $suppliers = Contact::query()
            ->whereRole('supplier')
            ->where('name', 'like', "%{$search}%")
            ->paginate(10);

        return response()->json($suppliers);
    }
}
