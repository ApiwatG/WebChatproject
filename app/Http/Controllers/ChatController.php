<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request, $roomId)
    {
        $message = $request->input('message');
        if (empty($message)) {
            return response()->json(['status' => 'error', 'error' => 'Message cannot be empty'], 422);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'error' => 'Not authenticated'], 401);
        }

        try {
            $cacheKey = "room:{$roomId}:messages";
            $messages = Cache::get($cacheKey, []);
            $messages[] = [
                'user' => $user->name,
                'message' => $message,
                'time' => now()->toIso8601String()
            ];
            Cache::put($cacheKey, $messages, 3600);

            event(new MessageSent($roomId, $user->name, $message, now()->toIso8601String()));

            return response()->json([
                'status' => 'success',
                'message' => 'Message sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => 'Failed to send message'
            ], 500);
        }
    }

    public function getMessages($roomId)
    {
        $cacheKey = "room:{$roomId}:messages";
        $messages = Cache::get($cacheKey, []);
        return response()->json($messages);
    }

    public function reportMessage(Request $request, $roomId)
    {
        \DB::table('reports')->insert([
            'room_id' => $roomId,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'created_at' => now()
        ]);
        return response()->json(['status' => 'success', 'message' => 'Message reported']);
    }
}