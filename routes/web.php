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

Route::get('/','App\Http\Controllers\PusherController@index');
Route::post('/broadcast','App\Http\Controllers\PusherController@broadcast');
Route::post('/receive','App\Http\Controllers\PusherController@receive');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
<<<<<<< Updated upstream
});
=======
    Route::get('/shop', [shopcontroller::class, 'index'])->name('shop.index');
    Route::get('/cosmetic', [cosmeticcontroller::class, 'index'])->name('cosmetic.index');
    Route::get('/rooms', [
        RoomController


            ::class,
        'index'
    ])->name('rooms.index');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::post('/rooms/{room}/join', [RoomController::class, 'join'])->name('rooms.join');





    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::post('/rooms/{room}/send', [RoomController::class, 'sendMessage'])->name('rooms.sendMessage');
    Route::post('/rooms/{room}/report', [RoomController::class, 'reportMessage'])->name('rooms.report');
    Route::get('/chat/{roomId}/messages', [ChatController::class, 'getMessages']);
    Route::post('/chat/{roomId}/send', [ChatController::class, 'sendMessage']);
    Route::post('/chat/{roomId}/report', [ChatController::class, 'reportMessage']);

});
<<<<<<< Updated upstream
<<<<<<< Updated upstream
=======
=======

Route::get('/test', function () {
    return view('test');
});




    
>>>>>>> Stashed changes



>>>>>>> Stashed changes



>>>>>>> Stashed changes

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