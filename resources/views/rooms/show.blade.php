
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Game Room</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/inroom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/game.css') }}">
</head>
<body>
<div class="wrap">
    {{-- Left Scene Area --}}
    <div class="scene">
        {{-- Your game/content area here --}}
        <div class="minilogo">
            <img src="{{ asset('img/logo.png') }}" alt="Game Logo" onerror="this.style.display='none'">
        </div>
    </div>

    {{-- Right Chat Panel --}}
    <div class="chatPanel">
        <div class="chatHeader">
            <h2>{{ $room->name }}</h2>
            <div class="uid">Room #{{ $room->id }}</div>
        </div>

        <div class="userList">
            <strong>Users ({{ $room->users()->count() }}/{{ $room->max_users }}):</strong>
            @foreach($room->users as $user)
                {{ $user->name }}{{ !$loop->last ? ', ' : '' }}
            @endforeach
        </div>

        <div id="messages" class="messages">
            {{-- Messages will load here --}}
        </div>

        <form id="chat-form" class="inputRow">
            @csrf
            <input 
                type="text" 
                id="message" 
                placeholder="Type your message..." 
                autocomplete="off"
            >
            <button type="submit">Send</button>
        </form>
    </div>
</div>
{{-- Sound Toggle Button --}}
<div class="sound" onclick="toggleSound()">
    🔊
</div>

<script>
    const roomId = "{{ $room->id }}";
    const currentUser = "{{ auth()->user()->name }}";
    let soundEnabled = true;

    function toggleSound() {
        soundEnabled = !soundEnabled;
        document.querySelector('.sound').textContent = soundEnabled ? '🔊' : '🔇';
    }

    function addMessage(user, message, isSent = false) {
        const messagesDiv = document.getElementById("messages");
        const msgDiv = document.createElement('div');
        msgDiv.className = isSent ? 'msg sent' : 'msg recv';
        
        if (!isSent) {
            msgDiv.innerHTML = `<strong>${user}:</strong> ${message}`;
        } else {
            msgDiv.innerHTML = `<strong>You:</strong> ${message}`;
        }
        
        messagesDiv.appendChild(msgDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    // Load cached messages
    fetch(`/chat/${roomId}/messages`)
        .then(res => res.json())
        .then(data => {
            data.forEach(msg => {
                const isSent = msg.user === currentUser;
                addMessage(msg.user, msg.message, isSent);
            });
        })
        .catch(err => console.error('Failed to load messages:', err));

    // Listen for game moves and messages
    if (window.Echo) {
        window.Echo.channel(`game.${roomId}`)
            .listen('GameMove', (e) => {
                // Update game board
                const cell = document.querySelector(`.ttt-cell[data-index="${e.position}"]`);
                board[e.position] = e.player;
                cell.textContent = e.player;
                cell.dataset.player = e.player;
                cell.classList.add('disabled');
                
                // Update game state
                if (e.gameState.winner) {
                    gameActive = false;
                    if (e.gameState.winner === 'Draw') {
                        document.getElementById('gameStatus').textContent = "It's a Draw!";
                    } else {
                        document.getElementById('gameStatus').textContent = `Player ${e.gameState.winner} Wins! 🎉`;
                        if (e.gameState.winningCells) {
                            e.gameState.winningCells.forEach(index => {
                                document.querySelector(`.ttt-cell[data-index="${index}"]`).style.backgroundColor = '#aaffaa';
                            });
                        }
                    }
                } else {
                    currentPlayer = e.player === 'X' ? 'O' : 'X';
                    document.getElementById('gameStatus').textContent = `Player ${currentPlayer}'s Turn`;
                }
            });
            
        window.Echo.join(`chat.${roomId}`)
            .here((users) => {
                console.log('Users currently in room:', users);
            })
            .joining((user) => {
                console.log(user.name + ' joined the room');
            })
            .leaving((user) => {
                console.log(user.name + ' left the room');
            })
            .listen('MessageSent', (e) => {
                addMessage(e.user, e.message, false);
                
                // Optional: Play sound notification
                if (soundEnabled && e.user !== currentUser) {
                    // You can add a notification sound here
                    console.log('New message received!');
                }
            });
    } else {
        console.error("Echo not initialized. Check bootstrap.js and run 'npm run dev'");
    }

    // Send message
    document.getElementById("chat-form").addEventListener("submit", function(e) {
        e.preventDefault();
        
        const messageInput = document.getElementById("message");
        const message = messageInput.value.trim();
        
        if (message === "") return;

        // Add message immediately to UI
        addMessage(currentUser, message, true);

        fetch(`/chat/${roomId}/send`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector("input[name='_token']").value
            },
            body: JSON.stringify({ message })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Message sent successfully');
        })
        .catch(err => {
            console.error('Failed to send message:', err);
        });

        messageInput.value = "";
    });

    // Allow Enter to send message
    document.getElementById("message").addEventListener("keypress", function(e) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            document.getElementById("chat-form").dispatchEvent(new Event('submit'));
        }
    });
</script>

<script>
    let board = ['', '', '', '', '', '', '', '', ''];
let currentPlayer = 'X';
let gameActive = true;

const winningConditions = [
    [0, 1, 2], [3, 4, 5], [6, 7, 8], // Rows
    [0, 3, 6], [1, 4, 7], [2, 5, 8], // Columns
    [0, 4, 8], [2, 4, 6]              // Diagonals
];

function openGame() {
    document.getElementById('gameOverlay').classList.add('active');
}

function closeGame() {
    document.getElementById('gameOverlay').classList.remove('active');
}

function resetGame() {
    board = ['', '', '', '', '', '', '', '', ''];
    currentPlayer = 'X';
    gameActive = true;
    document.getElementById('gameStatus').textContent = "Player X's Turn";
    
    document.querySelectorAll('.ttt-cell').forEach(cell => {
        cell.textContent = '';
        cell.classList.remove('disabled');
        cell.removeAttribute('data-player');
        cell.style.backgroundColor = '#f0f0f0';
    });
}

function checkWinner() {
    for (let condition of winningConditions) {
        const [a, b, c] = condition;
        if (board[a] && board[a] === board[b] && board[a] === board[c]) {
            return board[a];
        }
    }
    
    if (!board.includes('')) {
        return 'Draw';
    }
    
    return null;
}

function handleCellClick(e) {
    const cell = e.target;
    const index = parseInt(cell.dataset.index);
    
    if (board[index] !== '' || !gameActive) {
        return;
    }
    
    // Send move to server
    axios.post(`/game/${roomId}/move`, {
        position: index,
        player: currentPlayer,
        _token: document.querySelector('meta[name="csrf-token"]').content
    });
}
}

function getWinningCells() {
    for (let condition of winningConditions) {
        const [a, b, c] = condition;
        if (board[a] && board[a] === board[b] && board[a] === board[c]) {
            return [a, b, c];
        }
    }
    return null;
}

// Add click listeners to all cells
document.querySelectorAll('.ttt-cell').forEach(cell => {
    cell.addEventListener('click', handleCellClick);
});

// Close popup when clicking outside
document.getElementById('gameOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGame();
    }
});
</script>

<div class="game-button" onclick="openGame()">
    🎮
</div>

{{-- Game Popup --}}
<div class="game-overlay" id="gameOverlay">
    <div class="game-popup">
        <span class="game-close" onclick="closeGame()">&times;</span>
        <h2 style="text-align: center; margin-bottom: 10px;">Tic-Tac-Toe</h2>
        
        <div class="game-status" id="gameStatus">Player X's Turn</div>
        
        <div class="ttt-board" id="tttBoard">
            <div class="ttt-cell" data-index="0"></div>
            <div class="ttt-cell" data-index="1"></div>
            <div class="ttt-cell" data-index="2"></div>
            <div class="ttt-cell" data-index="3"></div>
            <div class="ttt-cell" data-index="4"></div>
            <div class="ttt-cell" data-index="5"></div>
            <div class="ttt-cell" data-index="6"></div>
            <div class="ttt-cell" data-index="7"></div>
            <div class="ttt-cell" data-index="8"></div>
        </div>
        
        <button class="game-reset" onclick="resetGame()">Reset Game</button>
    </div>
</div>

<script src="{{ asset('js/game.js') }}" defer></script>
</body>
</html>