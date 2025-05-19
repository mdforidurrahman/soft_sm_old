<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;

defineRoleBasedRoutes(function ($role) {
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('index', [PosController::class, 'index'])->name('index'); // 'pos.dashboard'
        Route::get('product', [PosController::class, 'product'])->name('product'); // 'pos.product'
        Route::get('report', [PosController::class, 'report'])->name('report'); // 'pos.report'
        Route::get('search-product', [PosController::class, 'searchProduct'])->name('searchProduct'); // 'pos.searchProduct'
        Route::post('store', [PosController::class, 'store'])->name('store');
        // Add the route for productsByCategory with the correct name
        Route::get('products-by-category/{categoryId}', [PosController::class, 'productsByCategory'])
            ->name('productsByCategory'); // 'pos.productsByCategory'


    });
    Route::prefix('expense')->name('expense.')->group(function () {
        Route::get('', [ExpenseController::class, 'index'])->name('index');
        Route::get('expense', [ExpenseController::class, 'index'])->name('expense');
        Route::get('edit/{expense}', [ExpenseController::class, 'edit'])->name('edit');
        Route::post('saveData', [ExpenseController::class, 'saveData'])->name('saveData');
        Route::put('updateData/{id}', [ExpenseController::class, 'updateData'])->name('updateData');
        Route::delete('{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        Route::get('{id}/download-invoice', [ExpenseController::class, 'downloadInvoice'])->name('downloadInvoice');
        Route::get('{id}/show-invoice', [ExpenseController::class, 'showInvoice'])->name('showInvoice');
        Route::delete('{id}', [ExpenseController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('expensecategory')->name('expensecategory.')->group(function () {
        // New Routes for Edit and Delete Expense Categories
        Route::get('addcategory', [ExpenseCategoryController::class, 'expenseCategoryData'])->name('addcategory');
        Route::post('storecategory', [ExpenseCategoryController::class, 'storecategory'])->name('storecategory');
        Route::get('editcategory/{id}', [ExpenseCategoryController::class, 'editCategory'])->name('editcategory');
        Route::post('updatecategory/{id}', [ExpenseCategoryController::class, 'updateCategory'])->name('updatecategory');
        Route::delete('deletecategory/{id}', [ExpenseCategoryController::class, 'destroy'])->name('deletecategory');
    });
    Route::prefix('report')->name('report.')->group(function () {
        // New Routes for Edit and Delete Expense Categories
        Route::get('index', [ReportController::class, 'index'])->name('index');
        Route::get('sells', [ReportController::class, 'sells'])->name('sells');
    });

    Route::get('/expense/{id}/download-invoice-pdf', [ExpenseController::class, 'downloadInvoicePDF'])
        ->name('expense.download.pdf');
});
