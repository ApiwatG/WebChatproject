<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class ChatLogController extends Controller
{
    public function index()
    {
        $rooms = Room::with('host')->latest('last_activity')->get();
        return view('admin.chatlog.index', compact('rooms'));
    }

    public function show($id)
    {
        $room = Room::with('members')->findOrFail($id);
        return view('admin.chatlog.view', compact('room'));
    }
}
