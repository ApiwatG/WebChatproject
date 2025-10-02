<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    public function sendMessage(Request $request, $roomId)
    {
        $message = $request->message;
        $user = auth()->user();

        $cacheKey = "room:{$roomId}:messages";
        $messages = Cache::get($cacheKey, []);
        $messages[] = ['user' => $user->name, 'message' => $message, 'time' => now()];
        Cache::put($cacheKey, $messages, 3600);

        broadcast(new MessageSent($roomId, $user->name, $message))->toOthers();

        return response()->json(['status' => 'Message Sent!']);
    }

    public function getMessages($roomId)
    {
        $cacheKey = "room:{$roomId}:messages";
        return response()->json(Cache::get($cacheKey, []));
    }

    public function reportMessage(Request $request, $roomId)
    {
        \DB::table('reports')->insert([
            'room_id' => $roomId,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'created_at' => now()
        ]);
        return response()->json(['status' => 'Reported']);
    }
}
