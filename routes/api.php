<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellsController;
use App\Http\Controllers\Admin\SupplierController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/




Route::resource('sell', SellsController::class);

Route::get('supplierss', [SellsController::class,'index'])
->name('contact.store');

Route::get('supplierss', [SellsController::class, 'index'])
    ->name('contact.store');



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/suppliers/search', [SupplierController::class, 'search'])
    ->name('api.suppliers.search');




