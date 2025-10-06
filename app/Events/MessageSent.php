<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $user;
    public $message;
    public $time;

    public function __construct($roomId, $user, $message, $time = null)
    {
        $this->roomId = $roomId;
        $this->user = $user;
        $this->message = $message;
        $this->time = $time ?? now()->toIso8601String();
    }

    public function broadcastOn()
    {
        return new PresenceChannel('chat.' . $this->roomId);
    }

    public function broadcastWith()
    {
        return [
            'user' => $this->user,
            'message' => $this->message,
            'time' => $this->time,
        ];
    }
}

