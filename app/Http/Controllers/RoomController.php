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

        // Automatically join the creator to the room
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

        // Check if room is now empty and delete it
        $activeUsersCount = $room->users()->wherePivot('is_inroom', true)->count();
        
        if ($activeUsersCount === 0) {
            $room->users()->detach(); // Clean up pivot records
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
        
        $room = Room::withCount('activeUsers')
                    ->havingRaw('active_users_count < max_users')
                    ->inRandomOrder()
                    ->first();
        
        if (!$room) {
            return back()->with('error', 'No available rooms');
        }
        
        return $this->join($room);
    }
}