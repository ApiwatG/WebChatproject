// Game state
let board = ["", "", "", "", "", "", "", "", ""];
let currentPlayer = "X";
let gameActive = true;

// Winning combinations
const winningConditions = [
    [0, 1, 2],
    [3, 4, 5],
    [6, 7, 8], // Rows
    [0, 3, 6],
    [1, 4, 7],
    [2, 5, 8], // Columns
    [0, 4, 8],
    [2, 4, 6], // Diagonals
];

function openGame() {
    document.getElementById("gameOverlay").classList.add("active");
    resetGame();
}

function closeGame() {
    document.getElementById("gameOverlay").classList.remove("active");
}

function resetGame() {
    board = ["", "", "", "", "", "", "", "", ""];
    currentPlayer = "X";
    gameActive = true;
    document.getElementById("gameStatus").textContent = "Player X's Turn";

    document.querySelectorAll(".ttt-cell").forEach((cell) => {
        cell.textContent = "";
        cell.classList.remove("disabled");
        cell.removeAttribute("data-player");
        cell.style.backgroundColor = "#f0f0f0";
    });
}

function checkWinner() {
    for (let condition of winningConditions) {
        const [a, b, c] = condition;
        if (board[a] && board[a] === board[b] && board[a] === board[c]) {
            return board[a];
        }
    }

    if (!board.includes("")) {
        return "Draw";
    }

    return null;
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

function handleCellClick(e) {
    const cell = e.target;
    const index = parseInt(cell.dataset.index);

    if (board[index] !== "" || !gameActive) {
        return;
    }

    board[index] = currentPlayer;
    cell.textContent = currentPlayer;
    cell.dataset.player = currentPlayer;
    cell.classList.add("disabled");

    const winner = checkWinner();

    if (winner) {
        gameActive = false;
        if (winner === "Draw") {
            document.getElementById("gameStatus").textContent = "It's a Draw!";
        } else {
            document.getElementById(
                "gameStatus"
            ).textContent = `Player ${winner} Wins! 🎉`;
            // Highlight winning cells
            const winningCells = getWinningCells();
            if (winningCells) {
                winningCells.forEach((index) => {
                    document.querySelector(
                        `.ttt-cell[data-index="${index}"]`
                    ).style.backgroundColor = "#aaffaa";
                });
            }
        }
        return;
    }

    currentPlayer = currentPlayer === "X" ? "O" : "X";
    document.getElementById(
        "gameStatus"
    ).textContent = `Player ${currentPlayer}'s Turn`;
}

// Initialize game when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    // Add click listeners to all cells
    document.querySelectorAll(".ttt-cell").forEach((cell) => {
        cell.addEventListener("click", handleCellClick);
    });

    // Close popup when clicking outside
    document
        .getElementById("gameOverlay")
        .addEventListener("click", function (e) {
            if (e.target === this) {
                closeGame();
            }
        });
});
