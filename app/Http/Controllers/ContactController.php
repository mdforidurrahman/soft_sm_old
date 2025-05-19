<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ProductCategory;
use App\Models\Sell;
use App\Models\SellPayment;
use App\Models\Store;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Contact::latest();
        if (Auth::User()->hasRole('admin')) {
            $storeName = Store::where('status', 1)->latest()->get();
        } else {
            $storeName = Auth::User()->stores;
        }
        if (request()->ajax()) {
            return DataTables::of($projects)
                ->addIndexColumn()
                ->editColumn('contact_id', function ($row) {
                    return $row->contact_id;
                })
                ->addColumn('father_name', function ($row) {  // Add this
                    return $row->father_name ?? 'N/A';       // Handle null values
                })
                ->addColumn('status', function ($row) {
                    return view('components.status-toggle', [
                        'id' => $row->id,
                        'model' => 'contact',
                        'status' => $row->status
                    ])->render();
                })
                ->addColumn('action', function ($row) {
                    return view('components.action-buttons', [
                        'id' => $row->id,
                        'model' => 'contact',
                        'editModal' => 'editStoreModal',
                        'editModalRoute' => 'contacts.edit',
                        'deleteRoute' => 'contacts.destroy',
                    ])->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.contacts.index', compact('storeName'));
    }


    public function customers()
    {
        $user = Auth::user();
        $projects = Contact::where('role', 'customer');

        if ($user->hasRole('admin')) {
            $storeName = Store::where('status', 1)->latest()->get();
            $projects = $projects->latest();
        } elseif ($user->hasRole('credit_officer') || $user->hasRole('area_manager')) {
            $storeName = Store::where('status', 1)->latest()->get();
            $projects = $projects->latest();
        } elseif ($user->hasRole('manager')) {
            $storeName = $user->stores;
            $projects = $projects->whereIn('store_id', $user->stores->pluck('id'))
                ->latest();
        } else {
            $storeName = $user->stores;
            $projects = $projects->where('created_by', $user->id)
                ->latest();
        }

        $categories = ProductCategory::where('status', 1)->get();

        if (request()->ajax()) {
            return DataTables::of($projects)
                ->addIndexColumn()
                ->editColumn('contact_id', function ($row) {
                    return $row->contact_id;
                })
                ->addColumn('father_name', function ($row) {
                    return $row->father_name ?? 'N/A';
                })
                ->addColumn('total_invoice', function ($row) {
                    $totalInvoice = Sell::where('customer_id', $row->id)->sum('net_total');
                    return number_format($totalInvoice, 2) . ' TK';
                })
                ->addColumn('total_paid', function ($row) {
                    $totalPaid = SellPayment::whereHas('sell', function ($query) use ($row) {
                        $query->where('customer_id', $row->id);
                    })->sum('amount');
                    return number_format($totalPaid, 2) . ' TK';
                })

                ->addColumn('balance_due', function ($row) {
                    $totalInvoice = Sell::where('customer_id', $row->id)->sum('net_total');
                    $totalPaid = SellPayment::whereHas('sell', function ($query) use ($row) {
                        $query->where('customer_id', $row->id);
                    })->sum('amount');
                    $balanceDue = $totalInvoice - $totalPaid;
                    return number_format($balanceDue, 2) . ' TK';
                })
                ->addColumn('image', function ($row) {
                    return '<img src="' . asset($row->image) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('finger_print', function ($row) {
                    return '<img src="' . asset($row->finger_print) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('signature', function ($row) {
                    return '<img src="' . asset($row->signature) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('status', function ($row) {
                    return view('components.status-toggle', [
                        'id' => $row->id,
                        'model' => 'contact',
                        'status' => $row->status
                    ])->render();
                })
                ->addColumn('action', function ($row) use ($user) {
                    $buttons = [
                        'id' => $row->id,
                        'model' => 'contact',
                        'ledgerModal' => 'viewLedgerModal',
                        'ledgerModalRoute' => 'customer.ledger',
                    ];

                    // Admin has full access
                    if ($user->hasRole('admin')) {
                        $buttons['payLedgerModal'] = 'payLedgerModal';
                        $buttons['payLedgerModalRoute'] = 'customer.pay.debt';
                        $buttons['editModal'] = 'editStoreModal';
                        $buttons['editModalRoute'] = 'contacts.edit';
                        $buttons['deleteRoute'] = 'contacts.destroy';
                    }
                    // Manager can pay but not edit/delete
                    elseif ($user->hasRole('manager')) {
                        $buttons['payLedgerModal'] = 'payLedgerModal';
                        $buttons['payLedgerModalRoute'] = 'customer.pay.debt';
                    }
                    // Area Manager and Credit Officer - only view ledger (no other buttons)
    
                    return view('components.action-buttons', $buttons)->render();
                })
                ->rawColumns(['action', 'status', 'image', 'finger_print', 'signature', 'total_invoice', 'total_paid', 'balance_due'])
                ->make(true);




        }

        return view('admin.contacts.customer-index', compact('storeName', 'categories'));
    }





    public function downloadLedgerPdf(Request $request, $customerId)
    {
        try {
            // Fetch customer
            $customer = Contact::findOrFail($customerId);

            // Validate and retrieve date range
            $dateRange = $request->input('date_range');
            if (!$dateRange || !str_contains($dateRange, ' - ')) {
                return back()->with('error', 'Invalid or missing date range.');
            }

            $storeId = $request->input('store_id', 'all');

            // Parse the date range (assuming format: mm/dd/yyyy - mm/dd/yyyy)
            $dates = explode(' - ', $dateRange);
            $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

            // Build transaction query
            $query = Transaction::where('customer_id', $customerId)
                ->whereBetween('date', [$startDate, $endDate]);

            if ($storeId !== 'all') {
                $query->where('store_id', $storeId);
            }

            // Fetch ordered transactions
            $transactions = $query->orderBy('date', 'asc')->get();

            // Calculate summary
            $summary = [
                'total_invoice' => $transactions->sum('debit'),
                'total_paid' => $transactions->sum('credit'),
                'balance_due' => $transactions->sum('debit') - $transactions->sum('credit'),
            ];

            // Load PDF view
            $pdf = Pdf::loadView('admin.contacts.pdfs.customer_ledger', [
                'customer' => $customer,
                'transactions' => $transactions,
                'summary' => $summary,
                'dateRange' => $dateRange,
            ])
                ->setPaper('a4')
                ->setOption('margin-top', 10);

            // Return PDF download
            return $pdf->download($customer->name . '_ledger_' . now()->format('Ymd') . '.pdf');

        } catch (\Exception $e) {
            Log::error('PDF Download Error: ' . $e->getMessage());
            return back()->with('error', 'Unable to generate PDF. Please check your input and try again.');
        }
    }


    public function supplier()
    {
        $projects = Contact::whereRole('supplier');

        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('manager')) {
            $storeName = Store::where('status', 1)->latest()->get();
            $projects = $projects->latest();
        } else {
            $storeName = Auth::user()->stores;
            $projects = $projects
                ->whereIn('store_id', Auth::user()->stores->pluck('id')) // make sure this is an array of IDs
                ->latest();
        }
        $categories = ProductCategory::where('status', 1)->get();
        if (request()->ajax()) {
            return DataTables::of($projects)
                ->addIndexColumn()
                ->editColumn('contact_id', function ($row) {
                    return $row->contact_id;
                })
                ->addColumn('father_name', function ($row) {  // Add this
                    return $row->father_name ?? 'N/A';       // Handle null values
                })
                ->addColumn('image', function ($row) {
                    return '<img src="' . asset($row->image) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('finger_print', function ($row) {
                    return '<img src="' . asset($row->finger_print) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('signature', function ($row) {
                    return '<img src="' . asset($row->signature) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('status', function ($row) {
                    return view('components.status-toggle', [
                        'id' => $row->id,
                        'model' => 'contact',
                        'status' => $row->status
                    ])->render();
                })
                ->addColumn('action', function ($row) {
                    return view('components.action-buttons', [
                        'id' => $row->id,
                        'model' => 'contact',
                        'editModal' => 'editStoreModal',
                        'editModalRoute' => 'contacts.edit',
                        'deleteRoute' => 'contacts.destroy',
                    ])->render();
                })
                ->rawColumns(['action', 'status', 'image', 'finger_print', 'signature'])
                ->make(true);
        }

        return view('admin.contacts.supplier-index', compact('storeName', 'categories'));
    }


    // ContactController.php

    public function getLedgerPay(Request $request, $id)
    {
        try {
            // Get regular sales
            $sells = Sell::with(['payments', 'store'])
                ->where('customer_id', $id)
                ->get();

            // Combine and format transactions for ledger
            $transactions = collect();

            // Add sells to transactions
            foreach ($sells as $sell) {
                // Add sale entry
                $transactions->push([
                    'date' => $sell->sell_date,
                    'type' => 'Sell',
                    'location' => optional($sell->store)->name ?? '-',
                    'payment_status' => $sell->payment_status,
                    'debit' => $sell->net_total,
                    'credit' => 0,
                    'payment_method' => '-',
                    'others' => "Invoice: {$sell->invoice_no}"
                ]);

                // Add payment entries
                foreach ($sell->payments as $payment) {
                    $transactions->push([
                        'date' => $payment->paid_on,
                        'type' => 'Payment',
                        'location' => optional($sell->store)->name ?? '-',
                        'payment_status' => $payment->payment_status,
                        'debit' => 0,
                        'credit' => $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'others' => $payment->payment_note ?? ''
                    ]);
                }
            }

            // Sort transactions by date
            $transactions = $transactions->sortBy('date');

            // Calculate summary
            $summary = [
                'total_invoice' => $transactions->where('type', 'Sale')->sum('debit')->sum('debit'),
                'total_paid' => $transactions->sum('credit'),
            ];

            // Calculate overall summary (all time)
            $overallQuery = Sell::where('customer_id', $id);

            if ($request->store_id !== 'all') {
                $overallQuery->where('store_id', $request->store_id);
            }

            $overallSells = $overallQuery->sum('net_total');

            // Get sell IDs for payments calculation
            $sellIds = $overallQuery->pluck('id');
            $overallPayments = SellPayment::whereIn('sell_id', $sellIds)->sum('amount');

            $summary['overall_invoice'] = $overallSells;
            $summary['overall_paid'] = $overallPayments;
            $summary['balance_due'] = $summary['overall_invoice'] - $summary['overall_paid'];

            // Get customer details
            $customer = Contact::findOrFail($id);

            $stores = Store::whereStatus(1)->select('id', 'name')->get();

            return response()->json([
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'father_name' => $customer->father_name,
                    'district' => $customer->district,
                    'thana' => $customer->thana,
                    'post_office' => $customer->post_office,
                    'village' => $customer->village,
                    'phone' => $customer->phone,
                    'business_name' => $customer->business_name ?? '-'
                ],
                'transactions' => $transactions->values(),
                'summary' => $summary,
                'stores' => $stores
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validation with custom messages
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'father_name' => 'required|string|max:255',
                'role' => 'required|string',
                'sales_type' => 'required|string',
                'nid' => 'required|string',
                'phone' => 'required|numeric|digits:11',
                'installment' => 'required|numeric',
                'district' => 'required|string',
                'thana' => 'required|string',
                'post_office' => 'required|string',
                'village' => 'required|string',
                'media_name' => 'required|string',
                'media_number' => 'required|string',
                'store_id' => 'required|exists:stores,id',
                'category_id' => 'required|exists:product_categories,id',
                'status' => 'required|integer|in:0,1',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
                'finger_print' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
                'signature' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
            ], [
                'name' => 'Customer name is required',

                'father_name' => 'Father name is required',

                'role' => 'Role is required',

                'sales_type' => 'Sales type is required',

                'nid' => 'NID is required',

                'phone' => 'Phone number is required & must be 11 digits',

                'installment' => 'Installment amount is required',

                'district' => 'District is required',

                'thana' => 'Thana is required',

                'post_office' => 'Post office is required',

                'village' => 'Village is required',

                'media_name' => 'Media/Grander name is required',

                'media_number' => 'Media/Grander number is required, Must be 11 Digit',

                'store_id' => 'Store selection is required',

                'category_id' => 'Category selection is required',

                'status' => 'Status is required',

                'image' => 'Customer Ledger Image must be an image file. jpeg, png, jpg, gif,not be greater than 10MB',

                'finger_print' => 'Customer NID Picture must be an image file. jpeg, png, jpg, gif,not be greater than 10MB',

                'signature' => 'Customers Picture must be an image file. jpeg, png, jpg, gif,not be greater than 10MB ',
            ]);


            // Generate contact_id if not provided
            $contact_id = $request->contact_id;
            if (!$contact_id) {
                $store = Store::findOrFail($validatedData['store_id']);
                $category = ProductCategory::findOrFail($validatedData['category_id']);

                $categoryAcronym = collect(explode(' ', $category->name))
                    ->map(fn($word) => Str::upper(Str::substr($word, 0, 1)))
                    ->join('');

                $storeAcronym = collect(explode(' ', $store->name))
                    ->map(fn($word) => Str::upper(Str::substr($word, 0, 1)))
                    ->join('');

                $baseAcronym = $storeAcronym . '_' . $categoryAcronym;

                $existingContacts = Contact::where('contact_id', $baseAcronym)
                    ->orWhere('contact_id', 'LIKE', $baseAcronym . '_%')
                    ->orderBy('contact_id', 'desc')
                    ->first();

                if (!$existingContacts) {
                    $contact_id = $baseAcronym;
                } else {
                    $contact_id = $existingContacts->contact_id === $baseAcronym
                        ? $baseAcronym . '_001'
                        : $baseAcronym . '_' . str_pad(
                            (int) Str::afterLast($existingContacts->contact_id, '_') + 1,
                            3,
                            '0',
                            STR_PAD_LEFT
                        );
                }
            }

            // Create contact
            $contact = Contact::create([
                'name' => $validatedData['name'],
                'father_name' => $validatedData['father_name'],
                'role' => $validatedData['role'],
                'sales_type' => $validatedData['sales_type'],
                'nid' => $validatedData['nid'],
                'phone' => $validatedData['phone'],
                'installment' => $validatedData['installment'],
                'district' => $validatedData['district'],
                'thana' => $validatedData['thana'],
                'post_office' => $validatedData['post_office'],
                'village' => $validatedData['village'],
                'media_name' => $validatedData['media_name'],
                'media_number' => $validatedData['media_number'],
                'store_id' => $validatedData['store_id'],
                'contact_id' => $contact_id,
                'created_by' => auth()->user()->id,
                'status' => $validatedData['status'],
                'product_category_id' => $validatedData['category_id']
            ]);

            // Handle file uploads
            $updates = [];
            foreach (['image', 'finger_print', 'signature'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $fileName = time() . '_' . $fileField . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/contacts'), $fileName);
                    $updates[$fileField] = 'uploads/contacts/' . $fileName;
                }
            }

            if (!empty($updates)) {
                $contact->update($updates);
            }

            return response()->json([
                'success' => true,
                'message' => 'Contact created successfully',
                'data' => $contact
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating contact: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Contact::findOrFail($id);
        return response()->json($data);
    }


    public function update(Request $request, $id)
    {
        try {
            // Validation with custom messages
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'father_name' => 'required|string|max:255',
                'role' => 'required|string',
                'sales_type' => 'required|string',
                'nid' => 'required|string',
                'phone' => 'required|numeric|digits:11',
                'installment' => 'required|numeric',
                'district' => 'required|string',
                'thana' => 'required|string',
                'post_office' => 'required|string',
                'village' => 'required|string',
                'media_name' => 'required|string',
                'media_number' => 'required|string',
                'contact_id' => 'nullable|string',
                'store_id' => 'required|exists:stores,id',
                'status' => 'required|integer|in:0,1',
                'category_id' => 'required|exists:product_categories,id',
                'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,svg,pdf,doc,docx,mp4,webm,ogg',
                'finger_print' => 'nullable|file|mimes:jpeg,jpg,png,gif,svg,pdf,doc,docx,mp4,webm,ogg',
                'signature' => 'nullable|file|mimes:jpeg,jpg,png,gif,svg,pdf,doc,docx,mp4,webm,ogg',
            ], [
                'name' => 'Customer name is required',

                'father_name' => 'Father name is required',

                'role' => 'Role is required',

                'sales_type' => 'Sales type is required',

                'nid' => 'NID is required',

                'phone' => 'Phone number is required & must be 11 digits',

                'installment' => 'Installment amount is required',

                'district' => 'District is required',

                'thana' => 'Thana is required',

                'post_office' => 'Post office is required',

                'village' => 'Village is required',

                'media_name' => 'Media/Grander name is required',

                'media_number' => 'Media/Grander number is required, Must be 11 Digit',

                'store_id' => 'Store selection is required',

                'category_id' => 'Category selection is required',

                'status' => 'Status is required',

                'image' => 'Customer Ledger Image must be an image file. jpeg, png, jpg, gif,not be greater than 10MB',

                'finger_print' => 'Customer NID Picture must be an image file. jpeg, png, jpg, gif,not be greater than 10MB',

                'signature' => 'Customers Picture must be an image file. jpeg, png, jpg, gif,not be greater than 10MB ',
            ]);


            // Generate contact_id if not provided
            $contact_id = $request->contact_id;
            if (!$contact_id) {
                $store = Store::findOrFail($validatedData['store_id']);
                $category = ProductCategory::findOrFail($validatedData['category_id']);

                $categoryAcronym = collect(explode(' ', $category->name))
                    ->map(fn($word) => Str::upper(Str::substr($word, 0, 1)))
                    ->join('');

                $storeAcronym = collect(explode(' ', $store->name))
                    ->map(fn($word) => Str::upper(Str::substr($word, 0, 1)))
                    ->join('');

                $baseAcronym = $storeAcronym . '_' . $categoryAcronym;

                $existingContacts = Contact::where('contact_id', $baseAcronym)
                    ->orWhere('contact_id', 'LIKE', $baseAcronym . '_%')
                    ->orderBy('contact_id', 'desc')
                    ->first();

                if (!$existingContacts) {
                    $contact_id = $baseAcronym;
                } else {
                    if ($existingContacts->contact_id === $baseAcronym) {
                        $contact_id = $baseAcronym . '_001';
                    } else {
                        $number = (int) Str::afterLast($existingContacts->contact_id, '_');
                        $contact_id = $baseAcronym . '_' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
                    }
                }
            }

            $contact = Contact::findOrFail($id);

            // File upload handling
            $updates = [];
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_image.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/contacts'), $imageName);
                $updates['image'] = 'uploads/contacts/' . $imageName;
            }
            if ($request->hasFile('finger_print')) {
                $finger = $request->file('finger_print');
                $fingerName = time() . '_finger.' . $finger->getClientOriginalExtension();
                $finger->move(public_path('uploads/contacts'), $fingerName);
                $updates['finger_print'] = 'uploads/contacts/' . $fingerName;
            }
            if ($request->hasFile('signature')) {
                $sign = $request->file('signature');
                $signName = time() . '_signature.' . $sign->getClientOriginalExtension();
                $sign->move(public_path('uploads/contacts'), $signName);
                $updates['signature'] = 'uploads/contacts/' . $signName;
            }

            // Update contact
            $contact->update(array_merge([
                'name' => $validatedData['name'],
                'father_name' => $validatedData['father_name'],
                'role' => $validatedData['role'],
                'sales_type' => $validatedData['sales_type'],
                'nid' => $validatedData['nid'],
                'phone' => $validatedData['phone'],
                'installment' => $validatedData['installment'],
                'district' => $validatedData['district'],
                'thana' => $validatedData['thana'],
                'post_office' => $validatedData['post_office'],
                'village' => $validatedData['village'],
                'media_name' => $validatedData['media_name'],
                'media_number' => $validatedData['media_number'],
                'contact_id' => $contact_id,
                'store_id' => $validatedData['store_id'],
                'product_category_id' => $validatedData['category_id'],
                'status' => $validatedData['status'],
                'updated_by' => Auth::id()
            ], $updates));

            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully',
                'data' => $contact
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating contact: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */


    public function storeImage(Request $request, $id)
    {
        try {
            // Validate the uploaded image
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240', // adjust max size as needed
            ]);

            // Find the contact
            $contact = Contact::findOrFail($id);

            // Handle the image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();

                // Store the image in the 'public' disk (usually storage/app/public directory)
                $path = $image->storeAs('contacts/images', $imageName, 'public');

                // Optionally save the image path in the database
                $contact->image_path = $path;
                $contact->save();
            }

            return response()->json(['message' => 'Image uploaded successfully!', 'path' => $path]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Image upload failed: ' . $e->getMessage()], 500);
        }
    }



    public function destroy(Request $request, $id)
    {
        try {
            $contact = Contact::findOrFail($id);
            foreach (['image', 'finger_print', 'signature'] as $field) {
                if ($contact->$field && file_exists(public_path($contact->$field))) {
                    @unlink(public_path($contact->$field));
                }
            }
            $contact->delete();
            $message = 'Contact deleted successfully';
            $type = 'success';
        } catch (\Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $type = 'error';
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => $type === 'success',
                'message' => $message,
                'notification' => compact('type', 'message')
            ]);
        }

        return redirect()->back();
    }



    public function searchCustomers(Request $request)
    {
        $searchTerm = $request->input('search');

        $query = Contact::query();

        // Apply search filter only if a search term is provided
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('contact_id', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Order by name for better user experience
        $query->orderBy('name', 'asc');

        // Get all matched customers
        $customers = $query->get();

        return response()->json([
            'data' => $customers,
            'total' => count($customers)
        ]);
    }




    /**
     * Process paid customers.
     */
    public function paidCustomers()
    {
        $user = Auth::user();

        $projects = Contact::where('role', 'customer')
            ->with([
                'sells' => function ($query) {
                    $query->select('id', 'customer_id', 'sell_date', 'payment_term', 'net_total', 'discount_amount', 'advance_balance', 'payment_due')
                        ->orderBy('sell_date', 'desc');
                },
                'sells.payments'
            ])
            ->whereHas('sells', function ($query) {
                $query->where('net_total', '>', 0);
            })
            ->whereDoesntHave('sells', function ($query) {
                $query->where('payment_due', '>', 0);
            });

        if ($user->hasRole('admin')) {
            $storeName = Store::where('status', 1)->latest()->get();
            $projects = $projects->latest();
        } elseif ($user->hasRole('credit_officer') || $user->hasRole('area_manager')) {
            $storeName = Store::where('status', 1)->latest()->get();
            $projects = $projects->latest();
        } elseif ($user->hasRole('manager')) {
            $storeName = $user->stores;
            $projects = $projects->whereIn('store_id', $user->stores->pluck('id'))
                ->latest();
        } else {
            $storeName = $user->stores;
            $projects = $projects->where('created_by', $user->id)
                ->latest();
        }

        $categories = ProductCategory::where('status', 1)->get();

        if (request()->ajax()) {
            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('sale_date', function ($row) {
                    return $row->sells->first() ? $row->sells->first()->sell_date : null;
                })
                ->addColumn('payment_term', function ($row) {
                    return $row->sells->first() ? $row->sells->first()->payment_term : null;
                })
                ->addColumn('total_invoice', function ($row) {
                    return $row->sells->sum('net_total');
                })
                ->addColumn('total_paid', function ($row) {
                    return $row->sells->sum(function ($sell) {
                        return $sell->payments->sum('amount');
                    });
                })
                ->addColumn('balance_due', function ($row) {
                    $totalInvoice = $row->sells->sum('net_total');
                    $totalPaid = $row->sells->sum(function ($sell) {
                        return $sell->payments->sum('amount');
                    });
                    return $totalInvoice - $totalPaid;
                })
                ->editColumn('contact_id', function ($row) {
                    return $row->contact_id;
                })
                ->addColumn('father_name', function ($row) {
                    return $row->father_name ?? 'N/A';
                })
                ->addColumn('image', function ($row) {
                    return '<img src="' . asset($row->image) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('finger_print', function ($row) {
                    return '<img src="' . asset($row->finger_print) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('signature', function ($row) {
                    return '<img src="' . asset($row->signature) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('status', function ($row) {
                    return view('components.status-toggle', [
                        'id' => $row->id,
                        'model' => 'contact',
                        'status' => $row->status
                    ])->render();
                })
                ->addColumn('action', function ($row) use ($user) {
                    $buttons = [
                        'id' => $row->id,
                        'model' => 'contact',
                        'ledgerModal' => 'viewLedgerModal',
                        'ledgerModalRoute' => 'customer.ledger',
                    ];

                    // Admin has full access
                    if ($user->hasRole('admin')) {
                        $buttons['payLedgerModal'] = 'payLedgerModal';
                        $buttons['payLedgerModalRoute'] = 'customer.pay.debt';
                        $buttons['editModal'] = 'editStoreModal';
                        $buttons['editModalRoute'] = 'contacts.edit';
                        $buttons['deleteRoute'] = 'contacts.destroy';
                    }
                    // Manager can pay but not edit/delete
                    elseif ($user->hasRole('manager')) {
                        $buttons['payLedgerModal'] = 'payLedgerModal';
                        $buttons['payLedgerModalRoute'] = 'customer.pay.debt';
                    }
                    // Area Manager and Credit Officer - only view ledger (no other buttons)
    
                    return view('components.action-buttons', $buttons)->render();
                })
                ->rawColumns(['action', 'status', 'image', 'finger_print', 'signature', 'total_invoice', 'total_paid', 'balance_due'])
                ->make(true);
        }

        return view('admin.contacts.paid-customer-index', compact('storeName', 'categories'));
    }



    //cash customers
    public function cashCustomers()
    {
        $user = Auth::user();

        $projects = Contact::whereRole('customer')
            ->with([
                'sells' => function ($query) {
                    $query->select('id', 'customer_id', 'sell_date', 'payment_term', 'net_total', 'discount_amount', 'advance_balance', 'payment_due')
                        ->orderBy('sell_date', 'desc');
                },
                'sells.payments'
            ])
            ->whereHas('sells', function ($query) {
                // At least one cash sale: full paid in advance
                $query->whereColumn('net_total', 'advance_balance')
                    ->where('net_total', '>', 0);
            })
            ->whereDoesntHave('sells', function ($query) {
                // No due left
                $query->where('payment_due', '>', 0);
            });

        if ($user->hasRole('admin')) {
            $storeName = Store::where('status', 1)->latest()->get();
            $projects = $projects->latest();
        } elseif ($user->hasRole('credit_officer') || $user->hasRole('area_manager')) {
            $storeName = Store::where('status', 1)->latest()->get();
            $projects = $projects->latest();
        } elseif ($user->hasRole('manager')) {
            $storeName = $user->stores;
            $projects = $projects->whereIn('store_id', $user->stores->pluck('id'))
                ->latest();
        } else {
            $storeName = $user->stores;
            $projects = $projects->where('created_by', $user->id)
                ->latest();
        }

        $categories = ProductCategory::where('status', 1)->get();

        if (request()->ajax()) {
            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('sale_date', function ($row) {
                    return $row->sells->first() ? $row->sells->first()->sell_date : null;
                })
                ->addColumn('payment_term', function ($row) {
                    return $row->sells->first() ? $row->sells->first()->payment_term : null;
                })
                ->addColumn('total_invoice', function ($row) {
                    return $row->sells->sum('net_total');
                })
                ->addColumn('total_discount', function ($row) {
                    return $row->sells->sum('discount_amount');
                })
                ->addColumn('total_paid', function ($row) {
                    return $row->sells->sum(function ($sell) {
                        return $sell->payments->sum('amount');
                    });
                })
                ->addColumn('balance_due', function ($row) {
                    $totalInvoice = $row->sells->sum('net_total');
                    $totalPaid = $row->sells->sum(function ($sell) {
                        return $sell->payments->sum('amount');
                    });
                    $balanceDue = $totalInvoice - $totalPaid;
                    return $balanceDue;
                })
                ->editColumn('contact_id', function ($row) {
                    return $row->contact_id;
                })
                ->addColumn('father_name', function ($row) {
                    return $row->father_name ?? 'N/A';
                })
                ->addColumn('image', function ($row) {
                    return '<img src="' . asset($row->image) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('finger_print', function ($row) {
                    return '<img src="' . asset($row->finger_print) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('signature', function ($row) {
                    return '<img src="' . asset($row->signature) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('status', function ($row) {
                    return view('components.status-toggle', [
                        'id' => $row->id,
                        'model' => 'contact',
                        'status' => $row->status
                    ])->render();
                })
                ->addColumn('action', function ($row) use ($user) {
                    $buttons = [
                        'id' => $row->id,
                        'model' => 'contact',
                        'ledgerModal' => 'viewLedgerModal',
                        'ledgerModalRoute' => 'customer.ledger',
                    ];

                    // Admin has full access
                    if ($user->hasRole('admin')) {
                        $buttons['payLedgerModal'] = 'payLedgerModal';
                        $buttons['payLedgerModalRoute'] = 'customer.pay.debt';
                        $buttons['editModal'] = 'editStoreModal';
                        $buttons['editModalRoute'] = 'contacts.edit';
                        $buttons['deleteRoute'] = 'contacts.destroy';
                    }
                    // Manager can pay but not edit/delete
                    elseif ($user->hasRole('manager')) {
                        $buttons['payLedgerModal'] = 'payLedgerModal';
                        $buttons['payLedgerModalRoute'] = 'customer.pay.debt';
                    }
                    // Area Manager and Credit Officer - only view ledger (no other buttons)
    
                    return view('components.action-buttons', $buttons)->render();
                })

                ->rawColumns(['action', 'status', 'image', 'finger_print', 'signature', 'total_invoice', 'total_paid', 'balance_due'])
                ->make(true);
        }

        return view('admin.contacts.cash-customer-index', compact('storeName', 'categories'));
    }


    //due Customers
    public function dueCustomers()
    {
        $user = Auth::user();
        $projects = Contact::where('role', 'customer')
       		->with([
        		'sells' => function ($query) {
            	$query->select('id', 'customer_id', 'sell_date', 'payment_term', 'net_total')
                ->orderBy('sell_date', 'desc');
        		},
        		'sells.payments'
    			]);

        if ($user->hasRole('admin')) {
            $storeName = Store::where('status', 1)->latest()->get();
            $projects = $projects->latest();
        } elseif ($user->hasRole('credit_officer') || $user->hasRole('area_manager')) {
            $storeName = Store::where('status', 1)->latest()->get();
            $projects = $projects->latest();
        } elseif ($user->hasRole('manager')) {
            $storeName = $user->stores;
            $projects = $projects->whereIn('store_id', $user->stores->pluck('id'))
                ->latest();
        } else {
            $storeName = $user->stores;
            $projects = $projects->where('created_by', $user->id)
                ->latest();
        }

        $categories = ProductCategory::where('status', 1)->get();

        if (request()->ajax()) {
            return DataTables::of($projects)
                ->addIndexColumn()
                ->editColumn('contact_id', function ($row) {
                    return $row->contact_id;
                })
                ->addColumn('father_name', function ($row) {
                    return $row->father_name ?? 'N/A';
                })
                ->addColumn('sale_date', function ($row) {
                    $sell = Sell::where('customer_id', $row->id)->orderBy('sell_date', 'desc')->first();
                    return $sell ? $sell->sell_date : null;
                })
                ->addColumn('payment_term', function ($row) {
                    $sell = Sell::where('customer_id', $row->id)->orderBy('sell_date', 'desc')->first();
                    return $sell ? $sell->payment_term : null;
                })
                // Only show customers with due
                ->filter(function ($query) {
                    $query->whereHas('sells', function ($q) {
                        $q->whereRaw('net_total > IFNULL((SELECT SUM(amount) FROM sell_payments WHERE sell_payments.sell_id = sells.id), 0)');
                    });
                })
                ->addColumn('total_invoice', function ($row) {
                    $totalInvoice = Sell::where('customer_id', $row->id)->sum('net_total');
                    return number_format($totalInvoice, 2) . ' TK';
                })
                ->addColumn('total_paid', function ($row) {
                    $totalPaid = SellPayment::whereHas('sell', function ($query) use ($row) {
                        $query->where('customer_id', $row->id);
                    })->sum('amount');
                    return number_format($totalPaid, 2) . ' TK';
                })
                ->addColumn('balance_due', function ($row) {
                    $totalInvoice = Sell::where('customer_id', $row->id)->sum('net_total');
                    $totalPaid = SellPayment::whereHas('sell', function ($query) use ($row) {
                        $query->where('customer_id', $row->id);
                    })->sum('amount');
                    $balanceDue = $totalInvoice - $totalPaid;
                    return number_format($balanceDue, 2) . ' TK';
                })
                ->addColumn('image', function ($row) {
                    return '<img src="' . asset($row->image) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('finger_print', function ($row) {
                    return '<img src="' . asset($row->finger_print) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('signature', function ($row) {
                    return '<img src="' . asset($row->signature) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('status', function ($row) {
                    return view('components.status-toggle', [
                        'id' => $row->id,
                        'model' => 'contact',
                        'status' => $row->status
                    ])->render();
                })
              
                      // Add this filter for proper searching
        ->filter(function ($query) {
            if (request()->has('search') && !empty(request()->search['value'])) {
                $search = request()->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('contact_id', 'like', "%{$search}%")
                      ->orWhereHas('sells', function($q) use ($search) {
                          $q->where('invoice_no', 'like', "%{$search}%");
                      });
                });
            }
            
            // Keep your existing due filter
            $query->whereHas('sells', function ($q) {
                $q->whereRaw('net_total > IFNULL((SELECT SUM(amount) FROM sell_payments WHERE sell_payments.sell_id = sells.id), 0)');
            });
        })
              
              
                ->addColumn('action', function ($row) use ($user) {
                    $buttons = [
                        'id' => $row->id,
                        'model' => 'contact',
                        'ledgerModal' => 'viewLedgerModal',
                        'ledgerModalRoute' => 'customer.ledger',
                    ];

                    // Admin has full access
                    if ($user->hasRole('admin')) {
                        $buttons['payLedgerModal'] = 'payLedgerModal';
                        $buttons['payLedgerModalRoute'] = 'customer.pay.debt';
                        $buttons['editModal'] = 'editStoreModal';
                        $buttons['editModalRoute'] = 'contacts.edit';
                        $buttons['deleteRoute'] = 'contacts.destroy';
                    }
                    // Manager can pay but not edit/delete
                    elseif ($user->hasRole('manager')) {
                        $buttons['payLedgerModal'] = 'payLedgerModal';
                        $buttons['payLedgerModalRoute'] = 'customer.pay.debt';
                    }
                    // Area Manager and Credit Officer - only view ledger (no other buttons)
    
                    return view('components.action-buttons', $buttons)->render();
                })
                ->rawColumns(['action', 'status', 'image', 'finger_print', 'signature', 'total_invoice', 'total_paid', 'balance_due'])
                ->make(true);
        }

        return view('admin.contacts.due-customer-index', compact('storeName', 'categories'));
    }



    //over Due  
    public function overdueCustomers()
    {
        $user = Auth::user();
        $today = now()->startOfDay();

        $projects = Contact::where('role', 'customer')
           ->with([
            'sells' => function ($query) {
                $query->select('id', 'customer_id', 'sell_date', 'payment_term', 'net_total', 'invoice_no')
                    ->orderBy('sell_date', 'desc');
            },
            'sells.payments'
        ]);

        if ($user->hasRole('admin')) {
            $storeName = Store::where('status', 1)->latest()->get();
            $projects = $projects->latest();
        } elseif ($user->hasRole('credit_officer') || $user->hasRole('area_manager')) {
            $storeName = Store::where('status', 1)->latest()->get();
            $projects = $projects->latest();
        } elseif ($user->hasRole('manager')) {
            $storeName = $user->stores;
            $projects = $projects->whereIn('store_id', $user->stores->pluck('id'))
                ->latest();
        } else {
            $storeName = $user->stores;
            $projects = $projects->where('created_by', $user->id)
                ->latest();
        }


        $categories = ProductCategory::where('status', 1)->get();

        if (request()->ajax()) {
            return DataTables::of($projects)
                ->addIndexColumn()
                ->filter(function ($query) use ($today) {
                    $query->whereHas('sells', function ($q) use ($today) {
                        // Sell is overdue if: (sell_date + payment_term in MONTH) < today AND not fully paid
                        $q->whereRaw("DATE_ADD(sell_date, INTERVAL CAST(payment_term AS UNSIGNED) MONTH) < ?", [$today])
                            ->whereRaw("net_total > IFNULL((SELECT SUM(amount) FROM sell_payments WHERE sell_payments.sell_id = sells.id), 0)");
                    });
                })
                ->editColumn('contact_id', function ($row) {
                    return $row->contact_id;
                })
                ->addColumn('father_name', function ($row) {
                    return $row->father_name ?? 'N/A';
                })
                ->addColumn('sale_date', function ($row) {
                    // Get most recent overdue unpaid sell for this customer
                    $sell = Sell::where('customer_id', $row->id)
                        ->whereRaw("DATE_ADD(sell_date, INTERVAL CAST(payment_term AS UNSIGNED) MONTH) < ?", [now()->startOfDay()])
                        ->whereRaw("net_total > IFNULL((SELECT SUM(amount) FROM sell_payments WHERE sell_payments.sell_id = sells.id), 0)")
                        ->orderBy('sell_date', 'desc')
                        ->first();
                    return $sell ? $sell->sell_date : null;
                })
                ->addColumn('payment_term', function ($row) {
                    $sell = Sell::where('customer_id', $row->id)
                        ->whereRaw("DATE_ADD(sell_date, INTERVAL CAST(payment_term AS UNSIGNED) MONTH) < ?", [now()->startOfDay()])
                        ->whereRaw("net_total > IFNULL((SELECT SUM(amount) FROM sell_payments WHERE sell_payments.sell_id = sells.id), 0)")
                        ->orderBy('sell_date', 'desc')
                        ->first();
                    return $sell ? $sell->payment_term : null;
                })
                ->addColumn('total_invoice', function ($row) {
                    $totalInvoice = Sell::where('customer_id', $row->id)->sum('net_total');
                    return number_format($totalInvoice, 2) . ' TK';
                })
                ->addColumn('total_paid', function ($row) {
                    $totalPaid = SellPayment::whereHas('sell', function ($query) use ($row) {
                        $query->where('customer_id', $row->id);
                    })->sum('amount');
                    return number_format($totalPaid, 2) . ' TK';
                })
                ->addColumn('balance_due', function ($row) {
                    $totalInvoice = Sell::where('customer_id', $row->id)->sum('net_total');
                    $totalPaid = SellPayment::whereHas('sell', function ($query) use ($row) {
                        $query->where('customer_id', $row->id);
                    })->sum('amount');
                    $balanceDue = $totalInvoice - $totalPaid;
                    return number_format($balanceDue, 2) . ' TK';
                })
                ->addColumn('image', function ($row) {
                    return '<img src="' . asset($row->image) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('finger_print', function ($row) {
                    return '<img src="' . asset($row->finger_print) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('signature', function ($row) {
                    return '<img src="' . asset($row->signature) . '" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">';
                })
                ->addColumn('status', function ($row) {
                    return view('components.status-toggle', [
                        'id' => $row->id,
                        'model' => 'contact',
                        'status' => $row->status
                    ])->render();
                })
              
                                    // Add this filter for proper searching
        ->filter(function ($query) {
            if (request()->has('search') && !empty(request()->search['value'])) {
                $search = request()->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('contact_id', 'like', "%{$search}%")
                      ->orWhereHas('sells', function($q) use ($search) {
                          $q->where('invoice_no', 'like', "%{$search}%");
                      });
                });
            }
            
            // Keep your existing due filter
            $query->whereHas('sells', function ($q) {
                $q->whereRaw('net_total > IFNULL((SELECT SUM(amount) FROM sell_payments WHERE sell_payments.sell_id = sells.id), 0)');
            });
        })
              
              
                ->addColumn('action', function ($row) use ($user) {
                    $buttons = [
                        'id' => $row->id,
                        'model' => 'contact',
                        'ledgerModal' => 'viewLedgerModal',
                        'ledgerModalRoute' => 'customer.ledger',
                    ];

                    // Admin has full access
                    if ($user->hasRole('admin')) {
                        $buttons['payLedgerModal'] = 'payLedgerModal';
                        $buttons['payLedgerModalRoute'] = 'customer.pay.debt';
                        $buttons['editModal'] = 'editStoreModal';
                        $buttons['editModalRoute'] = 'contacts.edit';
                        $buttons['deleteRoute'] = 'contacts.destroy';
                    }
                    // Manager can pay but not edit/delete
                    elseif ($user->hasRole('manager')) {
                        $buttons['payLedgerModal'] = 'payLedgerModal';
                        $buttons['payLedgerModalRoute'] = 'customer.pay.debt';
                    }
                    // Area Manager and Credit Officer - only view ledger (no other buttons)
    
                    return view('components.action-buttons', $buttons)->render();
                })
                ->rawColumns(['action', 'status', 'image', 'finger_print', 'signature', 'total_invoice', 'total_paid', 'balance_due'])
                ->make(true);
        }

        return view('admin.contacts.overdue-customer-index', compact('storeName', 'categories'));
    }


}