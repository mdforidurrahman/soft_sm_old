<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountTransactionController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\FileUploadController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AllReportController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BusinessLocationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTransferController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReturnPurchaseController;
use App\Http\Controllers\ReturnSellController;
use App\Http\Controllers\SellsController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreUserController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DealerController;

/**
 * @return void
 */


defineRoleBasedRoutes(function () {
	Route::get('/dashboard', [DashboardController::class, 'dashboard'])
		->name('dashboard');

	Route::resource('user', UserController::class);
	route::post('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');
	Route::resource('employee', EmployeeController::class);

	Route::resource('projects', ProjectController::class);

	// pos Requests
	Route::resource('contact', ContactController::class);
	Route::resource('stores', StoreController::class);
	Route::resource('contacts', ContactController::class);
	Route::resource('purchase', PurchaseController::class);
	Route::resource('sell', SellsController::class);

	// Return Purchase
	Route::get('/purchases/{purchaseId}/return', [PurchaseController::class, 'showReturn'])
		->name('purchase.return');

	Route::get('/purchases/{purchaseId}/returnable-items', [ReturnPurchaseController::class, 'getPurchaseItems'])
		->name('purchase.return.items');

	Route::post('/purchase-returns', [ReturnPurchaseController::class, 'store'])
		->name('purchase.return.store');

	Route::get('/purchase-returns', [ReturnPurchaseController::class, 'index'])
		->name('purchase.return.index');

	Route::get('/purchase-returns/{id}', [ReturnPurchaseController::class, 'show']);


	// Sell Return

	Route::get('/sells/{sellId}/return', [SellsController::class, 'showReturn'])
		->name('sells.return');

	Route::get('/sells/{sellId}/returnable-items', [ReturnSellController::class, 'getSellItems'])
		->name('sells.return.items');

	Route::post('/sells-returns', [ReturnSellController::class, 'store'])
		->name('sells.return.store');

	Route::get('/sells-returns', [ReturnSellController::class, 'index'])
		->name('sells.return.index');

	Route::get('/sells-returns/{id}', [ReturnSellController::class, 'show']);


	Route::resource('/product-category', ProductCategoryController::class);
	Route::resource('/product', ProductController::class);
	Route::resource('/business-location', BusinessLocationController::class);

	Route::resource('product-transfers', ProductTransferController::class)
		->only(['index']);

	Route::get('/product-transfers/history', [ProductTransferController::class, 'transferHistory'])->name('product-transfers.history');

	Route::post('/product-transfers/initiate', [ProductTransferController::class, 'initializeTransfer'])
		->name('product-transfers.initiate');

	Route::patch('/product-transfers/accept', [ProductTransferController::class, 'acceptTransfer'])
		->name('product-transfers.accept');

	Route::patch('/product-transfers/reject', [ProductTransferController::class, 'rejectTransfer'])
		->name('product-transfers.reject');


	// Reports Route
	Route::any('reports/profit-loss', [AllReportController::class, 'profitLoss'])
		->name('reports.profit-loss');
	Route::post('reports/profit-loss', [AllReportController::class, 'getProfitLossData'])
		->name('reports.profit-loss.data');
	Route::post('reports/profit-loss/export-pdf', [AllReportController::class, 'exportProfitLossPDF'])
		->name('reports.profit-loss.export-pdf');
	Route::get('reports/profit-loss/export-excel', [AllReportController::class, 'exportProfitLossExcel'])
		->name('reports.profit-loss.export-excel');

	// Return Purchase


    Route::get('customers/paid', [ContactController::class, 'paidCustomers'])->name('customers.paid');
  
    Route::get('customers/cash', [ContactController::class, 'cashCustomers'])->name('customers.cash');
  
    Route::get('customers/due', [ContactController::class, 'dueCustomers'])->name('customers.due');
  
    Route::get('customers/overdue', [ContactController::class, 'overdueCustomers'])->name('customers.overdue');

	// Custom Routes

	Route::get('/customers', [ContactController::class, 'customers'])
		->name('customers.index');

	Route::get('/supplier', [ContactController::class, 'supplier'])->name('supplier.index');

	Route::get('sell/{id}/download-invoice', [SellsController::class, 'downloadInvoice'])
		->name('sell.downloadInvoice');
	Route::get('sell/{id}/show-invoice', [SellsController::class, 'showInvoice'])
		->name('sell.showInvoice');


	Route::post('/stores/{store}/users', [StoreUserController::class, 'addUserToStore'])
		->name('stores.users.add');

	// Remove a user from a store
	Route::delete('/stores/{store}/users/{user}', [StoreUserController::class, 'removeUserFromStore'])
		->name('stores.users.remove');

	Route::get('/products/by-store', [ProductController::class, 'getProductsByStore'])
		->name('products.by-store');


	// APi Route
	Route::get('/customers/search', [ContactController::class, 'searchCustomers'])
		->name('customers.search');

	Route::resource('account', AccountController::class);
	Route::resource('banks', BankController::class);
	//Route::resource('withdrawals', WithdrawalController::class);
    Route::resource('withdrawals', WithdrawalController::class)->except(['show']);
	Route::resource('account-transactions', AccountTransactionController::class);

	Route::get('withdrawals/bank-accounts/{store_id}', [WithdrawalController::class, 'getBankAccounts'])
		->name('withdrawals.bank-accounts');
});


Route::get('/toggle-status/{model}/{id}', [AdminController::class, 'toggleStatus'])
	->name('toggleStatus');

Route::patch('projects/{project}/update-status', [ProjectController::class, 'updateStatus'])
	->name('projects.update-status');

Route::post('/file-upload', [FileUploadController::class, 'upload'])->name('file.upload');


Route::get('/get/products', [AdminController::class, 'getProducts'])
	->name('get.products');


Route::post('/users/{user}/assign-stores', [UserController::class, 'assignStores'])->name('users.assign-stores');
Route::get('/users/{user}/stores', [UserController::class, 'getUserStores'])->name('users.get-stores');

//    Ledger Route


Route::prefix('/customer/ledger')->group(function () {
	Route::get('/{id}', [CustomerPaymentController::class, 'getLedger'])->name('customer.ledger');
	Route::get('{id}/pay', [CustomerPaymentController::class, 'pay'])
		->name('customer.payment.details');
	Route::post('{id}/pay', [CustomerPaymentController::class, 'store'])
		->name('customer.payment.store');

	Route::get('/{customerId}/download', [CustomerPaymentController::class, 'downloadLedgerPdf'])
		->name('customer.ledger.download');
});


Route::post('/contacts/{id}/upload-image', [ContactController::class, 'storeImage'])->name('contacts.uploadImage');



Route::get('/account-transactions/summary', [AccountTransactionController::class, 'accountTransactionsSummary'])->name('admin.account-transactions.transactions-summary');


Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('dealers', DealerController::class);
});
