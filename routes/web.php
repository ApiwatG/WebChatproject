<?php

use Illuminate\Support\Facades\Route;

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



Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

use App\Http\Controllers\AdminController;

Route::middleware(['auth', 'verified'])->group(function () {
    // user ปกติ
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // admin เท่านั้น
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {

        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    });
});



