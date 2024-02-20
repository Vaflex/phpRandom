<!DOCTYPE html>
<html>
<head>
    <title>PHP Tetris</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        #game-board {
            border: 2px solid black;
            margin: 20px auto;
            display: inline-block;
        }
        .row {
            display: flex;
        }
        .cell {
            width: 20px;
            height: 20px;
            border: 1px solid #ccc;
            background-color: white;
        }
        .falling-piece {
            background-color: red; /* Změna barvy na červenou */
        }
    </style>
</head>
<body>
    <h1>PHP Tetris</h1>
    <div id="game-board"></div>

    <script>
        var board = [];
        var boardWidth = 10;
        var boardHeight = 20;
        var currentPiece = null;
        var intervalId = null;

        function initializeBoard() {
            for (var i = 0; i < boardHeight; i++) {
                board[i] = Array(boardWidth).fill(0);
            }
        }

        function createPiece() {
            var pieces = [
                [[1, 1, 1, 1]], // I
                [[1, 1], [1, 1]], // O
                [[0, 1, 0], [1, 1, 1]], // T
                [[1, 0], [1, 1], [0, 1]], // S
                [[0, 1], [1, 1], [1, 0]], // Z
                [[1, 1, 1], [0, 1, 0]], // L
                [[1, 1, 1], [1, 0, 0]]  // J
            ];
            var randomIndex = Math.floor(Math.random() * pieces.length);
            return pieces[randomIndex];
        }

        function drawPiece(piece, x, y) {
            for (var i = 0; i < piece.length; i++) {
                for (var j = 0; j < piece[i].length; j++) {
                    if (piece[i][j] === 1) {
                        board[y + i][x + j] = 1;
                    }
                }
            }
        }

        function clearBoard() {
            for (var i = 0; i < boardHeight; i++) {
                for (var j = 0; j < boardWidth; j++) {
                    board[i][j] = 0;
                }
            }
        }

        function renderBoard() {
            var gameBoard = document.getElementById('game-board');
            gameBoard.innerHTML = '';
            for (var i = 0; i < boardHeight; i++) {
                var row = document.createElement('div');
                row.classList.add('row');
                for (var j = 0; j < boardWidth; j++) {
                    var cell = document.createElement('div');
                    cell.classList.add('cell');
                    if (board[i][j] === 1) {
                        cell.style.backgroundColor = 'blue'; // Adjust color as needed
                    }
                    row.appendChild(cell);
                }
                gameBoard.appendChild(row);
            }

            // Draw the falling piece
            if (currentPiece) {
                var [x, y] = currentPiece.position;
                var piece = currentPiece.shape;
                for (var i = 0; i < piece.length; i++) {
                    for (var j = 0; j < piece[i].length; j++) {
                        if (piece[i][j] === 1) {
                            var cell = document.createElement('div');
                            cell.classList.add('cell', 'falling-piece'); // Přidáme třídu falling-piece pro barvení padajících dílků
                            cell.style.top = (y + i) * 20 + 'px';
                            cell.style.left = (x + j) * 20 + 'px';
                            gameBoard.appendChild(cell);
                        }
                    }
                }
            }
        }

        function moveDown() {
            if (currentPiece) {
                var [x, y] = currentPiece.position;
                if (!collision(x, y + 1)) {
                    currentPiece.position[1]++;
                } else {
                    drawPiece(currentPiece.shape, x, y);
                    clearLines();
                    currentPiece = {
                        shape: createPiece(),
                        position: [Math.floor(boardWidth / 2) - 1, 0]
                    };
                }
                renderBoard();
            }
        }

        function collision(x, y) {
            var piece = currentPiece.shape;
            for (var i = 0; i < piece.length; i++) {
                for (var j = 0; j < piece[i].length; j++) {
                    if (piece[i][j] === 1) {
                        if (x + j < 0 || x + j >= boardWidth || y + i >= boardHeight || board[y + i][x + j] === 1) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }

        function clearLines() {
            var linesCleared = 0;
            for (var i = boardHeight - 1; i >= 0; i--) {
                if (board[i].every(cell => cell === 1)) {
                    board.splice(i, 1);
                    board.unshift(Array(boardWidth).fill(0));
                    linesCleared++;
                    i++; // check the same row again after clearing
                }
            }
            return linesCleared;
        }

        function startGame() {
            initializeBoard();
            currentPiece = {
                shape: createPiece(),
                position: [Math.floor(boardWidth / 2) - 1, 0]
            };
            renderBoard();
            intervalId = setInterval(moveDown, 500); // Adjust speed as needed
        }

        function pauseGame() {
            clearInterval(intervalId);
        }

        function resumeGame() {
            intervalId = setInterval(moveDown, 500);
        }

        function rotatePiece() {
            if (currentPiece) {
                var rotatedPiece = [];
                var piece = currentPiece.shape;
                for (var i = 0; i < piece[0].length; i++) {
                    rotatedPiece.push([]);
                    for (var j = piece.length - 1; j >= 0; j--) {
                        rotatedPiece[i].push(piece[j][i]);
                    }
                }
                currentPiece.shape = rotatedPiece;
                renderBoard();
            }
        }

        document.addEventListener('keydown', function(event) {
            switch (event.keyCode) {
                case 37: // Left arrow
                    if (!collision(currentPiece.position[0] - 1, currentPiece.position[1])) {
                        currentPiece.position[0]--;
                        renderBoard();
                    }
                    break;
                case 39: // Right arrow
                    if (!collision(currentPiece.position[0] + 
