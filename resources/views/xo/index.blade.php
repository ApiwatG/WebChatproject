<!DOCTYPE html>
<html>
<head>
    <title>XO Game</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .xo-cell {
            transition: all 0.3s ease;
        }
        .xo-cell:hover:not(:disabled) {
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
        }
        .xo-cell.x {
            color: #3b82f6;
            text-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }
        .xo-cell.o {
            color: #ef4444;
            text-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
        }
        .player-badge {
            transition: all 0.3s ease;
        }
        .player-badge.active {
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.6);
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .refresh-notice {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body class="text-white flex items-center justify-center min-h-screen p-4">
    <div class="text-center max-w-md w-full">
        <h1 class="text-4xl mb-6 font-bold text-white drop-shadow-lg">Tic Tac Toe</h1>

        @if(isset($roomId))
            <!-- Room-based multiplayer game -->
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 mb-4">
                <div class="flex justify-around mb-4">
                    <div class="player-badge px-4 py-2 rounded-lg bg-gray-800/50" id="player-x">
                        <span class="text-blue-400 font-bold">X:</span>
                        <span id="player-x-name">{{ $players['X'] ?? 'Waiting...' }}</span>
                    </div>
                    <div class="player-badge px-4 py-2 rounded-lg bg-gray-800/50" id="player-o">
                        <span class="text-red-400 font-bold">O:</span>
                        <span id="player-o-name">{{ $players['O'] ?? 'Waiting...' }}</span>
                    </div>
                </div>
                
                <div class="text-sm text-gray-300 mb-2" id="game-status">
                    @if($winner)
                        {{ $winner === 'Draw' ? "It's a Draw!" : "Winner: " . ($players[$winner] ?? $winner) }}
                    @else
                        {{ isset($players[$turn]) ? $players[$turn] . "'s turn ($turn)" : 'Waiting for players...' }}
                    @endif
                </div>
                
                <div class="text-xs text-gray-400 refresh-notice mb-4">🔄 Auto-refreshing every 2 seconds</div>
            </div>

            <div class="grid grid-cols-3 gap-3 w-full max-w-xs mx-auto mb-6">
                @foreach ($board as $i => $cell)
                    <button 
                        class="xo-cell w-full aspect-square bg-gray-800/50 backdrop-blur-sm rounded-xl text-5xl font-bold hover:bg-gray-700/50 disabled:opacity-50 disabled:cursor-not-allowed {{ $cell === 'X' ? 'x' : ($cell === 'O' ? 'o' : '') }}"
                        data-index="{{ $i }}"
                        onclick="makeMove({{ $i }})"
                        {{ $cell || $winner ? 'disabled' : '' }}>
                        {{ $cell }}
                    </button>
                @endforeach
            </div>

            <div class="flex gap-4 justify-center mb-4">
                <button 
                    onclick="resetGame()"
                    class="bg-blue-500 px-6 py-3 rounded-lg hover:bg-blue-600 font-semibold shadow-lg transform hover:scale-105 transition">
                    New Game
                </button>
                <a 
                    href="{{ route('rooms.show', $roomId) }}"
                    class="bg-gray-600 px-6 py-3 rounded-lg hover:bg-gray-700 font-semibold shadow-lg transform hover:scale-105 transition inline-block">
                    Back to Room
                </a>
            </div>

            <script>
                const roomId = "{{ $roomId }}";
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const currentUser = "{{ auth()->user()->name ?? 'Guest' }}";
                
                let pollingInterval;

                // Start polling for game state
                function startPolling() {
                    pollingInterval = setInterval(loadGameState, 2000);
                }

                function loadGameState() {
                    fetch(`/xo/room/${roomId}/state`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(updateBoard)
                    .catch(err => console.error('Failed to load game state:', err));
                }

                function updateBoard(gameState) {
                    const cells = document.querySelectorAll('.xo-cell');
                    
                    cells.forEach((cell, i) => {
                        const value = gameState.board[i];
                        cell.textContent = value;
                        cell.className = 'xo-cell w-full aspect-square bg-gray-800/50 backdrop-blur-sm rounded-xl text-5xl font-bold hover:bg-gray-700/50 disabled:opacity-50 disabled:cursor-not-allowed';
                        
                        if (value === 'X') cell.classList.add('x');
                        if (value === 'O') cell.classList.add('o');
                        
                        const canMove = !value && !gameState.winner;
                        const isMyTurn = gameState.players[gameState.turn] === currentUser;
                        cell.disabled = !canMove || !isMyTurn;
                    });

                    // Update player names
                    document.getElementById('player-x-name').textContent = gameState.players['X'] || 'Waiting...';
                    document.getElementById('player-o-name').textContent = gameState.players['O'] || 'Waiting...';
                    
                    // Update active player
                    document.getElementById('player-x').classList.toggle('active', gameState.turn === 'X' && !gameState.winner);
                    document.getElementById('player-o').classList.toggle('active', gameState.turn === 'O' && !gameState.winner);
                    
                    // Update status
                    const status = document.getElementById('game-status');
                    if (gameState.winner) {
                        if (gameState.winner === 'Draw') {
                            status.textContent = "It's a Draw!";
                        } else {
                            status.textContent = `Winner: ${gameState.players[gameState.winner]} (${gameState.winner})`;
                        }
                        
                        // Redirect back to room after 3 seconds when game ends
                        setTimeout(() => {
                            window.location.href = `/rooms/${roomId}`;
                        }, 3000);
                    } else if (gameState.players[gameState.turn]) {
                        status.textContent = `${gameState.players[gameState.turn]}'s turn (${gameState.turn})`;
                    } else {
                        status.textContent = 'Waiting for players...';
                    }
                }

                function makeMove(index) {
                    fetch(`/xo/room/${roomId}/move/${index}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.error) {
                            console.error(data.error);
                            return;
                        }
                        updateBoard(data);
                    })
                    .catch(err => console.error('Failed to make move:', err));
                }

                function resetGame() {
                    fetch(`/xo/room/${roomId}/reset`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(updateBoard)
                    .catch(err => console.error('Failed to reset game:', err));
                }

                // Start polling when page loads
                startPolling();

                // Clean up on page unload
                window.addEventListener('beforeunload', () => {
                    if (pollingInterval) clearInterval(pollingInterval);
                });
            </script>

        @else
            <!-- Standalone single-player/local game -->
            <div class="grid grid-cols-3 gap-3 w-full max-w-xs mx-auto mb-6">
                @foreach ($board as $i => $cell)
                    <form method="POST" action="{{ route('xo.move', $i) }}">
                        @csrf
                        <button 
                            class="xo-cell w-full aspect-square bg-gray-800/50 backdrop-blur-sm rounded-xl text-5xl font-bold hover:bg-gray-700/50 {{ $cell === 'X' ? 'x' : ($cell === 'O' ? 'o' : '') }}"
                            {{ $cell || $winner ? 'disabled' : '' }}>
                            {{ $cell }}
                        </button>
                    </form>
                @endforeach
            </div>

            @if ($winner)
                <h2 class="text-3xl mb-6 font-bold text-yellow-300 drop-shadow-lg">
                    {{ $winner === 'Draw' ? "It's a Draw!" : "Winner: $winner" }}
                </h2>
            @else
                <h2 class="text-2xl mb-6 text-gray-200">Turn: <span class="font-bold">{{ $turn }}</span></h2>
            @endif

            <div class="flex gap-4 justify-center">
                <form method="POST" action="{{ route('xo.reset') }}">
                    @csrf
                    <button class="bg-blue-500 px-6 py-3 rounded-lg hover:bg-blue-600 font-semibold shadow-lg transform hover:scale-105 transition">
                        Reset Game
                    </button>
                </form>

                <a href="{{ route('dashboard') }}" class="bg-gray-600 px-6 py-3 rounded-lg hover:bg-gray-700 font-semibold shadow-lg transform hover:scale-105 transition inline-block">
                    ← Back to Dashboard
                </a>
            </div>
        @endif
    </div>
</body>
</html>