<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Cache;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::withCount('users')->get();
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
        if ($room->isFull()) {
            return back()->with('error', 'Room is full.');
        }

        $room->users()->syncWithoutDetaching(auth()->id());

        return redirect()->route('rooms.show', $room->id);
    }

  public function show(Room $room)
{
    $room->load('users');

    $messages = Cache::get('room_'.$room->id.'_messages', []);

    return view('rooms.show', compact('room', 'messages'));
}

  
}
