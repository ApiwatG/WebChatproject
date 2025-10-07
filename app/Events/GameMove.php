<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameMove implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $player;
    public $position;
    public $gameBoard;
    public $currentPlayer;
    public $gameStatus;

    public function __construct($roomId, $player, $position, $gameBoard, $currentPlayer, $gameStatus)
    {
        $this->roomId = $roomId;
        $this->player = $player;
        $this->position = $position;
        $this->gameBoard = $gameBoard;
        $this->currentPlayer = $currentPlayer;
        $this->gameStatus = $gameStatus;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('chat.' . $this->roomId);
    }

    public function broadcastAs()
    {
        return 'game.move';
    }
}