<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameResponse implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $responder;
    public $accepted;

    public function __construct($roomId, $responder, $accepted)
    {
        $this->roomId = $roomId;
        $this->responder = $responder;
        $this->accepted = $accepted;
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->roomId);
    }
}