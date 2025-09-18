<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
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

Route::get('/home', function () {
    return view('home');
})->name('home');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // เปลี่ยนจาก twilight เป็น home
    Route::get('/home', [HomeController::class, 'index'])->name('home.index');
    Route::post('/home/join', [HomeController::class, 'join'])->name('home.join');
    
    // หรือถ้าต้องการให้เป็น default หลัง login
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
});