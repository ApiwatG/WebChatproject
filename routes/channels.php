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

use App\Models\User;
use App\Models\Room;

Broadcast::channel('room.{roomId}', function (User $user, $roomId) {
    return Room::find($roomId)?->users()->where('user_id', $user->id)->exists();
});