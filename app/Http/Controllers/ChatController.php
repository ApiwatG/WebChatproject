<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    protected function broadcastAndCacheMessage($roomId, $user, $message)
    {
        $cacheKey = "room:{$roomId}:messages";
        $messages = Cache::get($cacheKey, []);
        $messages[] = ['user' => is_object($user) ? $user->name : $user, 'message' => $message, 'time' => now()];
        Cache::put($cacheKey, $messages, 3600);

        broadcast(new MessageSent($user, $message, $roomId))->toOthers();
    }

    public function sendMessage(Request $request, $roomId)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $message = $request->message;
        $user = auth()->user();

        $this->broadcastAndCacheMessage($roomId, $user, $message);

        return response()->json(['status' => 'Message Sent!']);
    }

    public function getMessages($roomId)
    {
        $cacheKey = "room:{$roomId}:messages";
        return response()->json(Cache::get($cacheKey, []));
    }

    public function reportMessage(Request $request, $roomId)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        DB::table('reports')->insert([
            'room_id' => $roomId,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'created_at' => now()
        ]);
        return response()->json(['status' => 'Reported']);
    }


    public function messages($roomId)
    {
        try {
            $room = Room::with('messages.user')->findOrFail($roomId);
            $rows = $room->messages()->latest()->take(100)->get()->reverse()->map(function($m){
                return ['user' => $m->user->name, 'message' => $m->body];
            });
            return response()->json($rows);
        } catch (\Throwable $e) {
            $cacheKey = "room:{$roomId}:messages";
            return response()->json(Cache::get($cacheKey, []));
        }
    }

    public function send(Request $request, $roomId)
    {
        $request->validate(['message' => 'required|string|max:2000']);
        $user = Auth::user();

        Log::info('Chat send called', ['user_id'=> $user->id, 'room'=>$roomId, 'message'=>$request->message]);

        $this->broadcastAndCacheMessage($roomId, $user, $request->message);

        return response()->json(['ok' => true]);
    }
}
