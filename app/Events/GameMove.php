<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameMove implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $player;
    public $position;
    public $gameState;

    public function __construct($roomId, $player, $position, $gameState)
    {
        $this->roomId = $roomId;
        $this->player = $player;
        $this->position = $position;
        $this->gameState = $gameState;
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->roomId);
    }
}