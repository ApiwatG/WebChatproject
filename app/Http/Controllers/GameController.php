<?php

namespace App\Http\Controllers;

use App\Events\GameMove;
use Illuminate\Http\Request;

use App\Services\TicTacToeBot;

class GameController extends Controller
{
    protected $winningConditions = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8], // Rows
        [0, 3, 6], [1, 4, 7], [2, 5, 8], // Columns
        [0, 4, 8], [2, 4, 6]             // Diagonals
    ];

    protected $bot;

    public function request(Request $request, $roomId)
    {
        $event = new GameRequest($roomId, $request->requester);
        broadcast($event);
        return response()->json(['status' => 'success', 'event' => $event]);
    }

    public function accept(Request $request, $roomId)
    {
        broadcast(new GameResponse($roomId, $request->responder, true));
        return response()->json(['status' => 'success']);
    }

    public function decline(Request $request, $roomId)
    {
        broadcast(new GameResponse($roomId, $request->responder, false));
        return response()->json(['status' => 'success']);
    }

    public function move(Request $request, $roomId)
    {
        $position = $request->position;
        $player = $request->player;
        $isBot = $request->isBot ?? false;
        
        // ถ้าเป็นการเล่นกับบอท
        if ($isBot) {
            $board = $request->board;
            $gameState = $this->checkGameState(['position' => $position, 'player' => $player, 'board' => $board]);
            
            // ถ้าเกมยังไม่จบ ให้บอทเล่น
            if (!$gameState['winner']) {
                $board[$position] = $player;
                $botMove = $this->bot->makeMove($board);
                if ($botMove !== null) {
                    $board[$botMove] = 'O';
                    $gameState = $this->checkGameState(['position' => $botMove, 'player' => 'O', 'board' => $board]);
                    
                    return response()->json([
                        'status' => 'success',
                        'botMove' => $botMove,
                        'gameState' => $gameState
                    ]);
                }
            }
            
            return response()->json([
                'status' => 'success',
                'gameState' => $gameState
            ]);
        }
        
        // Create game state
        $gameState = $this->checkGameState($request->all());
        
        // Broadcast the move
        broadcast(new GameMove($roomId, $player, $position, $gameState))->toOthers();
        
        return response()->json(['status' => 'success']);
    }

    public function reset(Request $request, $roomId)
    {
        broadcast(new GameMove($roomId, 'X', null, ['reset' => true]))->toOthers();
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