<?php

use App\Http\Controllers\Admin\SupplierController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\DashboardRedirect;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\Frontend\FrontendController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */


 Route::get('/',[FrontendController::class,'index'])->name('index');



// Redirect Route to by his role

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardRedirectController::class, 'index'])->name('dashboard');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/ratul.php';





Route::get('/suppliers/search', [SupplierController::class, 'search'])
    ->name('api.suppliers.search');
