<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CosmeticController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\XOGameController;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ChatLogController;
use App\Http\Controllers\Admin\AdminCosmeticController;

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

// Root route - redirect to login
Route::get('/', function () {
    return redirect('/login');
});

// Authenticated User Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'banned'
])->group(function () {
    
    // Dashboard - redirect admin to admin dashboard
    Route::get('/dashboard', function () {
        if (auth()->user()->is_admin) {
            return redirect('/admin/dashboard');
        }
        return view('dashboard');
    })->name('dashboard');
    

Route::get('/xo', [XOGameController::class, 'index'])->name('xo.index');
Route::post('/xo/move/{index}', [XOGameController::class, 'move'])->name('xo.move');
Route::post('/xo/reset', [XOGameController::class, 'reset'])->name('xo.reset');

    // Shop Routes
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
    Route::post('/shop/{cosmetic}/purchase', [ShopController::class, 'purchase'])->name('shop.purchase');
    
    // Cosmetic Routes
    Route::get('/cosmetic', [CosmeticController::class, 'index'])->name('cosmetic.index');
    Route::post('/cosmetic/{cosmetic}/equip', [CosmeticController::class, 'equip'])->name('cosmetic.equip');
    Route::post('/cosmetic/{cosmetic}/unequip', [CosmeticController::class, 'unequip'])->name('cosmetic.unequip');
    
    // Room Routes
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::post('/rooms/{room}/join', [RoomController::class, 'join'])->name('rooms.join');
    Route::post('/rooms/{room}/leave', [RoomController::class, 'leave'])->name('rooms.leave');
    Route::post('/rooms/join-by-code', [RoomController::class, 'joinByCode'])->name('rooms.joinByCode');
    Route::post('/rooms/quick-join', [RoomController::class, 'quickJoin'])->name('rooms.quickJoin');
    
    // Chat Routes
    Route::get('/chat/{roomId}/messages', [ChatController::class, 'getMessages']);
    Route::post('/chat/{roomId}/send', [ChatController::class, 'sendMessage']);
    Route::post('/chat/{roomId}/report', [ChatController::class, 'reportMessage']);
    
    // Report Routes
    Route::post('/report/{offender}', [ReportController::class, 'store'])->name('report.store');

    
});

// Admin Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'admin'
])->prefix('admin')->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Ban User Management
    Route::get('/ban-user', [AdminController::class, 'banuser'])->name('banuser');
    Route::post('/ban/{userId}', [AdminController::class, 'ban'])->name('ban');
    Route::post('/unban/{userId}', [AdminController::class, 'unbanUser'])->name('unban');
    
    // Report Management
    Route::post('/report/dismiss/{id}', [AdminController::class, 'dismissReport'])->name('report.dismiss');

    
    // Chat Log
    Route::get('/chatlog', [ChatLogController::class, 'index'])->name('chatlog.index');
    Route::get('/chatlog/{id}', [ChatLogController::class, 'show'])->name('chatlog.show');
    
    // Cosmetics
   
});