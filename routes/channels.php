<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use App\Models\Room;

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

Broadcast::channel('presence-chat.{roomId}', function (User $user, $roomId) {
    // authorize only if user belongs to room
    $allowed = Room::where('id', $roomId)
        ->whereHas('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->exists();

    return $allowed ? ['id' => $user->id, 'name' => $user->name] : false;
});