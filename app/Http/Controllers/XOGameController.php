<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\Cache;

class XOGameController extends Controller
{
    public function index(Request $request)
    {
        $board = $request->session()->get('xo_board', array_fill(0, 9, ''));
        $turn = $request->session()->get('xo_turn', 'X');
        $winner = $this->checkWinner($board);

        return view('xo.index', compact('board', 'turn', 'winner'));
    }

    public function move(Request $request, $index)
    {
        $board = $request->session()->get('xo_board', array_fill(0, 9, ''));
        $turn = $request->session()->get('xo_turn', 'X');

        if ($board[$index] === '' && !$this->checkWinner($board)) {
            $board[$index] = $turn;
            $turn = $turn === 'X' ? 'O' : 'X';
        }

        $request->session()->put('xo_board', $board);
        $request->session()->put('xo_turn', $turn);

        return redirect()->route('xo.index');
    }

    public function reset(Request $request)
    {
        $request->session()->forget(['xo_board', 'xo_turn']);
        return redirect()->route('xo.index');
    }

    // Room-based game methods using Cache instead of broadcasting
    public function getRoomGame(Request $request, $roomId)
    {
        $room = Room::findOrFail($roomId);
        
        // Check if user is in the room
        if (!$room->users->contains(auth()->id())) {
            return response()->json(['error' => 'Not in room'], 403);
        }

        // Get game state from cache (shared across all users)
        $gameState = Cache::get("xo_room_{$roomId}", [
            'board' => array_fill(0, 9, ''),
            'turn' => 'X',
            'players' => [],
            'winner' => null,
            'lastUpdate' => time()
        ]);

        return response()->json($gameState);
    }

    public function roomMove(Request $request, $roomId, $index)
    {
        $room = Room::findOrFail($roomId);
        
        // Check if user is in the room
        if (!$room->users->contains(auth()->id())) {
            return response()->json(['error' => 'Not in room'], 403);
        }

        // Lock to prevent race conditions
        $lock = Cache::lock("xo_room_lock_{$roomId}", 10);
        
        try {
            if (!$lock->get()) {
                return response()->json(['error' => 'Game is being updated'], 423);
            }

            $gameState = Cache::get("xo_room_{$roomId}", [
                'board' => array_fill(0, 9, ''),
                'turn' => 'X',
                'players' => [],
                'winner' => null,
                'lastUpdate' => time()
            ]);

            // Assign players
            if (empty($gameState['players'])) {
                $gameState['players'] = ['X' => auth()->user()->name, 'O' => null];
            } elseif (!isset($gameState['players']['O']) && $gameState['players']['X'] !== auth()->user()->name) {
                $gameState['players']['O'] = auth()->user()->name;
            }

            // Check if it's the player's turn
            $currentPlayer = $gameState['players'][$gameState['turn']] ?? null;
            if ($currentPlayer !== auth()->user()->name) {
                return response()->json(['error' => 'Not your turn'], 403);
            }

            // Make move
            if ($gameState['board'][$index] === '' && !$gameState['winner']) {
                $gameState['board'][$index] = $gameState['turn'];
                $gameState['winner'] = $this->checkWinner($gameState['board']);
                
                if (!$gameState['winner']) {
                    $gameState['turn'] = $gameState['turn'] === 'X' ? 'O' : 'X';
                }
                
                $gameState['lastUpdate'] = time();
            }

            // Store in cache for 1 hour
            Cache::put("xo_room_{$roomId}", $gameState, 3600);

            return response()->json($gameState);
            
        } finally {
            $lock->release();
        }
    }

    public function resetRoomGame(Request $request, $roomId)
    {
        $room = Room::findOrFail($roomId);
        
        // Check if user is in the room
        if (!$room->users->contains(auth()->id())) {
            return response()->json(['error' => 'Not in room'], 403);
        }

        $gameState = [
            'board' => array_fill(0, 9, ''),
            'turn' => 'X',
            'players' => [],
            'winner' => null,
            'lastUpdate' => time()
        ];

        // Store in cache for 1 hour
        Cache::put("xo_room_{$roomId}", $gameState, 3600);

        return response()->json($gameState);
    }

    private function checkWinner($b)
    {
        $lines = [
            [0,1,2],[3,4,5],[6,7,8],
            [0,3,6],[1,4,7],[2,5,8],
            [0,4,8],[2,4,6],
        ];

        foreach ($lines as [$a, $b1, $c]) {
            if ($b[$a] && $b[$a] === $b[$b1] && $b[$a] === $b[$c]) {
                return $b[$a];
            }
        }

        return in_array('', $b) ? null : 'Draw';
    }
}