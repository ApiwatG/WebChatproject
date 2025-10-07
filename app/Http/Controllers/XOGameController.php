<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
