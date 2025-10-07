<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameStateUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $gameState;

    public function __construct($roomId, $gameState)
    {
        $this->roomId = $roomId;
        $this->gameState = $gameState;
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->roomId);
    }

    public function broadcastAs()
    {
        return 'GameStateUpdated';
    }
}