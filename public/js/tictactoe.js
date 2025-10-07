class TicTacToeGame {
    constructor(roomId, currentUser) {
        this.roomId = roomId;
        this.currentUser = currentUser;
        this.board = ["", "", "", "", "", "", "", "", ""];
        this.currentPlayer = "X";
        this.gameActive = true;
        this.playerSymbol = "";
        this.isMyTurn = false;
        this.gameRequestTimeout = null;
        this.pendingGameRequest = false;

        this.winningConditions = [
            [0, 1, 2],
            [3, 4, 5],
            [6, 7, 8], // Rows
            [0, 3, 6],
            [1, 4, 7],
            [2, 5, 8], // Columns
            [0, 4, 8],
            [2, 4, 6], // Diagonals
        ];

        this.initializeGame();
    }

    initializeGame() {
        this.setupWebSocket();
        this.setupEventListeners();
    }

    setupWebSocket() {
        if (!window.Echo) {
            console.error("Echo not initialized");
            setTimeout(() => this.setupWebSocket(), 1000);
            return;
        }

        const gameChannel = window.Echo.channel(`game.${this.roomId}`);

        gameChannel.listen(".GameRequest", (e) => {
            console.log("Game request received:", e);
            if (e.requester !== this.currentUser) {
                this.pendingGameRequest = true;
                document
                    .getElementById("gameRequestOverlay")
                    .classList.add("active");
                document.getElementById(
                    "gameRequestStatus"
                ).textContent = `${e.requester} wants to play Tic-Tac-Toe!`;
                document.getElementById("gameRequestButtons").style.display =
                    "flex";
            }
        });

        gameChannel.listen(".GameResponse", (e) => {
            clearTimeout(this.gameRequestTimeout);
            if (e.accepted) {
                if (e.responder !== this.currentUser) {
                    document
                        .getElementById("gameRequestOverlay")
                        .classList.remove("active");
                    document
                        .getElementById("gameOverlay")
                        .classList.add("active");
                }
            } else {
                if (e.responder !== this.currentUser) {
                    document
                        .getElementById("gameRequestOverlay")
                        .classList.remove("active");
                    alert("Game request was declined");
                }
            }
        });

        gameChannel.listen(".GameMove", (e) => {
            const cell = document.querySelector(
                `.ttt-cell[data-index="${e.position}"]`
            );
            this.board[e.position] = e.player;
            cell.textContent = e.player;
            cell.dataset.player = e.player;
            cell.classList.add("disabled");

            if (e.gameState.winner) {
                this.gameActive = false;
                if (e.gameState.winner === "Draw") {
                    document.getElementById("gameStatus").textContent =
                        "It's a Draw!";
                } else {
                    document.getElementById(
                        "gameStatus"
                    ).textContent = `Player ${e.gameState.winner} Wins! 🎉`;
                    if (e.gameState.winningCells) {
                        e.gameState.winningCells.forEach((index) => {
                            document.querySelector(
                                `.ttt-cell[data-index="${index}"]`
                            ).style.backgroundColor = "#aaffaa";
                        });
                    }
                }
            } else {
                this.currentPlayer = e.player === "X" ? "O" : "X";
                this.isMyTurn = this.currentPlayer === this.playerSymbol;
                document.getElementById("gameStatus").textContent = `Player ${
                    this.currentPlayer
                }'s Turn${this.isMyTurn ? " (Your turn)" : ""}`;
            }
        });
    }

    setupEventListeners() {
        // Add click listeners to all cells
        document.querySelectorAll(".ttt-cell").forEach((cell) => {
            cell.addEventListener("click", (e) => this.handleCellClick(e));
        });

        // Game control buttons
        const gameBtn = document.querySelector(".game-button");
        if (gameBtn) {
            gameBtn.addEventListener("click", () =>
                this.startMultiplayerGame()
            );
        }

        // Close popup when clicking outside
        document
            .getElementById("gameOverlay")
            .addEventListener("click", (e) => {
                if (e.target === e.currentTarget) {
                    this.closeGame();
                }
            });
    }

    handleCellClick(e) {
        const cell = e.target;
        const index = parseInt(cell.dataset.index);

        if (!this.gameActive) {
            console.log("Game is not active");
            return;
        }

        if (!this.isMyTurn) {
            console.log("Not your turn");
            return;
        }

        if (this.board[index] !== "") {
            console.log("Cell already occupied");
            return;
        }

        // Send move to server
        axios
            .post(`/game/${this.roomId}/move`, {
                position: index,
                player: this.playerSymbol,
                board: this.board,
                _token: document.querySelector('meta[name="csrf-token"]')
                    .content,
            })
            .catch((error) => {
                console.error("Error sending move:", error);
                alert("Failed to send move. Please try again.");
            });
    }

    startMultiplayerGame() {
        console.log("Sending game request...");

        axios
            .post(`/game/${this.roomId}/request`, {
                requester: this.currentUser,
                _token: document.querySelector('meta[name="csrf-token"]')
                    .content,
            })
            .then((response) => {
                console.log("Game request sent:", response);
                document
                    .getElementById("gameRequestOverlay")
                    .classList.add("active");
                document.getElementById("gameRequestStatus").textContent =
                    "Waiting for other player to accept...";
                document.getElementById("gameRequestButtons").style.display =
                    "none";

                this.gameRequestTimeout = setTimeout(() => {
                    this.closeGameRequest();
                    alert("Game request timed out");
                }, 30000);
            })
            .catch((error) => {
                console.error("Error sending game request:", error);
                alert("Failed to send game request. Please try again.");
            });
    }

    closeGameRequest() {
        const overlay = document.getElementById("gameRequestOverlay");
        if (overlay) {
            overlay.classList.remove("active");
        }
        this.pendingGameRequest = false;
        clearTimeout(this.gameRequestTimeout);
    }

    closeGame() {
        document.getElementById("gameOverlay").classList.remove("active");
        this.resetGame();
    }

    resetGame() {
        axios
            .post(`/game/${this.roomId}/reset`, {
                _token: document.querySelector('meta[name="csrf-token"]')
                    .content,
            })
            .then(() => {
                this.board = ["", "", "", "", "", "", "", "", ""];
                this.currentPlayer = "X";
                this.isMyTurn = this.playerSymbol === "X";
                this.gameActive = true;
                document.getElementById(
                    "gameStatus"
                ).textContent = `Player X's Turn${
                    this.isMyTurn ? " (Your turn)" : ""
                }`;

                document.querySelectorAll(".ttt-cell").forEach((cell) => {
                    cell.textContent = "";
                    cell.classList.remove("disabled");
                    cell.removeAttribute("data-player");
                    cell.style.backgroundColor = "#f0f0f0";
                });
            });
    }
}

// Initialize game when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    const roomId = document.querySelector('meta[name="room-id"]').content;
    const currentUser = document.querySelector(
        'meta[name="user-name"]'
    ).content;
    window.game = new TicTacToeGame(roomId, currentUser);
});
