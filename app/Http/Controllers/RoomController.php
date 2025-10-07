<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\RoomParticipant;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::withCount([
            'activeUsers as active_users_count'
        ])->withCount('users')->get();

        return view('rooms.index', compact('rooms'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50']);

        $room = Room::create([
            'name' => $request->name,
            'max_users' => 4
        ]);


        $userId = Auth::id();
        $room->users()->attach($userId, [
            'is_inroom' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('rooms.show', $room->id);
    }

    public function join(Room $room)
    {
        $userId = Auth::id();
        if ($room->isFull() && ! $room->users()->where('users.id', $userId)->exists()) {
            return back()->with('error', 'Room is full.');
        }
        if ($room->users()->where('users.id', $userId)->exists()) {
            $room->users()->updateExistingPivot($userId, [
                'is_inroom' => true,
                'updated_at' => now(),
            ]);
        } else {
            $room->users()->attach($userId, [
                'is_inroom' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('rooms.show', $room->id);
    }

    public function leave(Request $request, Room $room)
    {
        $userId = Auth::id();

        if ($room->users()->where('users.id', $userId)->exists()) {
            $room->users()->updateExistingPivot($userId, [
                'is_inroom' => false,
                'updated_at' => now(),
            ]);
        }


        $activeUsersCount = $room->users()->wherePivot('is_inroom', true)->count();
        
        if ($activeUsersCount === 0) {
            $room->users()->detach(); 
            $room->delete();
        }

        return redirect()->route('rooms.index');
    }

    public function show($id)
    {
        $room = Room::with(['users' => function ($q) {
            $q->wherePivot('is_inroom', true);
        }])->findOrFail($id);

        return view('rooms.show', compact('room'));
    }

    public function joinByCode(Request $request)
    {
        $request->validate(['room_code' => 'required|string']);
        
        $room = Room::where('id', $request->room_code)
                    ->first();
        
        if (!$room) {
            return back()->with('error', 'Room not found');
        }
        
        return $this->join($room);
    }

public function quickJoin()
{
    $userId = Auth::id();

    $room = Room::whereRaw('(select count(*) from `users` 
    inner join `room_participants` on `users`.`id` = `room_participants`
    .`user_id` where `rooms`.`id` = `room_participants`.`room_id`
     and `room_participants`.`is_inroom` = 1) < max_users')
                ->inRandomOrder()
                ->first();

    if (!$room) {
        return back()->with('error', 'No available rooms');
    }

    return $this->join($room);
}
}