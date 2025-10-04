<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        Room::create([
            'name' => $request->name,
            'max_users' => 4
        ]);

        return redirect()->route('rooms.index');
    }

    public function join(Room $room)
    {
        $userId = Auth::id();

        // Prevent joining if room is full and user is not already in
        if ($room->isFull() && ! $room->users()->where('users.id', $userId)->exists()) {
            return back()->with('error', 'Room is full.');
        }

        // attach or update pivot to set is_inroom = true
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

        return redirect()->route('rooms.index');
    }

    public function show($id)
    {
        // load room and only include users currently in room (is_inroom = true)
        $room = Room::with(['users' => function ($q) {
            $q->wherePivot('is_inroom', true);
        }])->findOrFail($id);

        return view('rooms.show', compact('room'));
    }
}
