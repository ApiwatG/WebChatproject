<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::withCount('users')->get();
        return view('rooms.index', compact('rooms'));
    }

    public function join(Room $room)
    {
        $user = auth()->user();

        if ($room->isFull()) {
            return redirect()->back()->with('error', 'Room is full');
        }

        $room->users()->syncWithoutDetaching($user->id);

        return redirect()->route('rooms.show', $room->id);
    }

    public function show(Room $room)
    {
        return view('rooms.show', compact('room'));
    }

    public function create(Request $request)
    {
        $room = Room::create([
            'name' => $request->name,
            'max_users' => 4,
        ]);

        return redirect()->route('rooms.index');
    }
}
