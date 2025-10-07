<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

<<<<<<< Updated upstream
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
=======
use App\Models\User;
use App\Models\Room;

Broadcast::channel('chat.{roomId}', function (User $user, $roomId) {
<<<<<<< Updated upstream
    return true; // ตรวจสอบสิทธิ์ตามที่ต้องการ
});
>>>>>>> Stashed changes
=======
    return [
        'id' => $user->id,
        'name' => $user->name
    ];
});
>>>>>>> Stashed changes
