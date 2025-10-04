<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $message;
    public $roomId;

    public function __construct($user, $message, $roomId)
    {
        // store username (string) and ensure roomId is scalar (int/string)
        $this->user = is_object($user) ? ($user->name ?? (string)$user) : (string)$user;
        $this->message = $message;
        $this->roomId = is_object($roomId) ? ($roomId->id ?? (int)$roomId) : (string)$roomId;
    }

    public function broadcastOn()
    {
        // ensure channel name is valid string like "chat.1"
        return new Channel("chat.{$this->roomId}");
    }

    public function broadcastWith()
    {
        return [
            'user' => $this->user,
            'message' => $this->message,
        ];
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }
}

