<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\cosmeticcontroller;
use App\Http\Controllers\shopcontroller;
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
    Route::get('/shop', [shopcontroller::class, 'index'])->name('shop.index');
    Route::get('/cosmetic', [cosmeticcontroller::class, 'index'])->name('cosmetic.index');
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::post('/rooms/{room}/join', [RoomController::class, 'join'])->name('rooms.join');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::post('/rooms/{room}/send', [RoomController::class, 'sendMessage'])->name('rooms.sendMessage');
    Route::post('/rooms/{room}/report', [RoomController::class, 'reportMessage'])->name('rooms.report');
    Route::get('/chat/{roomId}/messages', [ChatController::class, 'getMessages']);
    Route::post('/chat/{roomId}/send', [ChatController::class, 'sendMessage']);
    Route::post('/chat/{roomId}/report', [ChatController::class, 'reportMessage']);

});
    

