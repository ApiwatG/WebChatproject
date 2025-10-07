<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
<<<<<<< Updated upstream
use Illuminate\Support\Facades\Auth;
=======
require __DIR__ . '/vendor/autoload.php';
use Pusher\Pusher;



$newmessage ;

$result = $pusher->trigger('messages_sent', 'new-message', ['message' => $newmessage]);
if ($result) {
    echo "ส่งข้อมูลสำเร็จ!";
} else {
    echo "เกิดข้อผิดพลาดในการส่งข้อมูล";
}
>>>>>>> Stashed changes

class ChatController extends Controller
{
    public function sendMessage(Request $request, $roomId)
    {
<<<<<<< Updated upstream
        $message = $request->input('message');
        if (empty($message)) {
            return response()->json(['status' => 'error', 'error' => 'Message cannot be empty'], 422);
        }
=======
        $request->validate([
            'message' => 'required|string'
        ]);

        $message = $request->input('message');
        $user = auth()->user();
>>>>>>> Stashed changes

        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'error' => 'Not authenticated'], 401);
        }

<<<<<<< Updated upstream
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
=======
        $cacheKey = "room:{$roomId}:messages";
        $messages = Cache::get($cacheKey, []);
<<<<<<< Updated upstream
        $time = now()->toIso8601String();
        $messages[] = ['user' => $user->name, 'message' => $message, 'time' => $time];
=======
        $messages[] = [
            'user' => $user->name,
            'message' => $message,
            'time' => now()->toIso8601String()
        ];
>>>>>>> Stashed changes
        Cache::put($cacheKey, $messages, 3600);

<<<<<<< Updated upstream
        broadcast(new MessageSent($roomId, $user->name, $message, $time))->toOthers();
>>>>>>> Stashed changes

<<<<<<< Updated upstream
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
=======
        $event = new MessageSent($roomId, $user->name, $message);
        broadcast($event)->toOthers();

        \Log::info('Message broadcast:', [
            'room' => $roomId,
            'user' => $user->name,
            'message' => $message
        ]);

        return response()->json(['status' => 'Message Sent!', 'event' => $event]);
>>>>>>> Stashed changes
=======
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'user' => $user->name
        ]);
>>>>>>> Stashed changes
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