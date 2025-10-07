<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameRequest implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $requester;

    public function __construct($roomId, $requester)
    {
        $this->roomId = $roomId;
        $this->requester = $requester;
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->roomId);
    }

    public function broadcastAs()
    {
        return 'GameRequest';
    }
}