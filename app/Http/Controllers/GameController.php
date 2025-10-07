<?php

namespace App\Http\Controllers;

use App\Events\GameMove;
use Illuminate\Http\Request;

class GameController extends Controller
{
    protected $winningConditions = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8], // Rows
        [0, 3, 6], [1, 4, 7], [2, 5, 8], // Columns
        [0, 4, 8], [2, 4, 6]             // Diagonals
    ];

    public function move(Request $request, $roomId)
    {
        $position = $request->position;
        $player = $request->player;
        
        // Create game state
        $gameState = $this->checkGameState($request->all());
        
        // Broadcast the move
        broadcast(new GameMove($roomId, $player, $position, $gameState))->toOthers();
        
        return response()->json(['status' => 'success']);
    }

    protected function checkGameState($gameData)
    {
        $board = $gameData['board'] ?? array_fill(0, 9, '');
        $board[$gameData['position']] = $gameData['player'];
        
        // Check for winner
        foreach ($this->winningConditions as $condition) {
            [$a, $b, $c] = $condition;
            if ($board[$a] && $board[$a] === $board[$b] && $board[$a] === $board[$c]) {
                return [
                    'winner' => $board[$a],
                    'winningCells' => $condition
                ];
            }
        }
        
        // Check for draw
        if (!in_array('', $board)) {
            return ['winner' => 'Draw'];
        }
        
        return ['winner' => null];
    }
}