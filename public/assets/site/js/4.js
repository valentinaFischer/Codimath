const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');

const asteroids = [];
const operations = ["+", "-"];
let score = 0;
let lives = 5;
let gameOver = false;
let startTime; 
let scoreSaved = false;
let asteroidsLost = 0; 
let livesLostByErrors = 0; 

const backgroundImage = new Image();
backgroundImage.src = '/public/assets/site/images/background4.png'; // Caminho para a imagem de fundo

const asteroidImage = new Image();
asteroidImage.src = '/public/assets/site/images/asteroid.png'; // Caminho para a imagem dos asteroides

const heartImage = new Image();
heartImage.src = '/public/assets/site/images/heart.png';

let imagesLoaded = 0;
const totalImages = 3; 

function checkImagesLoaded() {
    imagesLoaded++;
    if (imagesLoaded === totalImages) {
        startGame();
    }
};

backgroundImage.onload = checkImagesLoaded;
asteroidImage.onload = checkImagesLoaded;
heartImage.onload = checkImagesLoaded;

backgroundImage.onerror = function() {
    console.error('Erro ao carregar a imagem de fundo');
};

asteroidImage.onerror = function() {
    console.error('Erro ao carregar a imagem dos asteroides');
};

heartImage.onerror = function() {
    console.error('Erro ao carregar a imagem do coração');
};

const asteroidSize = 50; // Tamanho da imagem dos asteroides
const heartSize = 30; // Tamanho da imagem do coração
const heartsMargin = 10;

function generateRandomOperation() {
    const num1 = Math.floor(Math.random() * 10) + 1;
    const num2 = Math.floor(Math.random() * 10) + 1;
    const operation = operations[Math.floor(Math.random() * operations.length)];

    let expression, result;

    if (operation === "+") {
        result = num1 + num2;
        expression = `${num1} ${operation} ${num2}`;
    } else if (operation === "-") {
        const [minuend, subtrahend] = num1 >= num2 ? [num1, num2] : [num2, num1];
        result = minuend - subtrahend;
        expression = `${minuend} ${operation} ${subtrahend}`;
    }

    return { expression, result };
};

function drawHearts() {
    for (let i = 0; i < lives; i++) {
        ctx.drawImage(heartImage, canvas.width - heartSize - heartsMargin - (i * (heartSize + heartsMargin)), heartsMargin, heartSize, heartSize);
    }
};

function createAsteroid() {
    const { expression, result } = generateRandomOperation();
    const x = Math.random() * (canvas.width - asteroidSize);
    const y = -asteroidSize;
    asteroids.push({ x, y, expression, result });
};

function updateGame() {
    if (gameOver) return;

    // Limpa o canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Desenha a imagem de fundo
    ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);

    asteroids.forEach((asteroid, index) => {
        asteroid.y += 0.2;

        // Desenha a imagem do asteroide
        ctx.drawImage(asteroidImage, asteroid.x, asteroid.y, asteroidSize, asteroidSize);

        // Define a fonte e a cor para o texto
        ctx.font = "20px Arial";
        ctx.fillStyle = "white";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";

        // Desenha o texto (expressão) no centro da imagem do asteroide
        ctx.fillText(asteroid.expression, asteroid.x + asteroidSize / 2, asteroid.y + asteroidSize / 2);

        if (asteroid.y > canvas.height) {
            lives--;
            asteroidsLost++;
            asteroids.splice(index, 1);
            checkGameOver();
        }
    });

    drawHearts();
};

function checkGameOver() {
    if (lives <= 0) {
        gameOver = true;
        showGameOverScreen();
    }
};

function showGameOverScreen() {
    // Desenha o fundo escuro
    ctx.fillStyle = "rgba(0, 0, 0, 0.7)";
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Exibe a mensagem de Game Over
    ctx.font = "40px Arial";
    ctx.fillStyle = "white";
    ctx.textAlign = "center";
    ctx.fillText("Você perdeu!", canvas.width / 2, canvas.height / 2 - 40);
    ctx.font = "30px Arial";
    ctx.fillText(`Sua pontuação: ${score}`, canvas.width / 2, canvas.height / 2);
    ctx.font = "25px Arial";
    ctx.fillText("Pressione Enter para jogar de novo", canvas.width / 2, canvas.height / 2 + 40);

    gameOverMessage = `Sua pontuação: ${score}`;
    // Salva o score e envia o formulário se ainda não tiver sido salvo
    if (!scoreSaved) {
        scoreSaved = true;
        sendGameData(gameOverMessage);
        document.getElementById("formPontuacao").submit();
    }
};

function sendGameData(gameOverMessageSend) {
    const pontuacaoFinal = score;
    const tempoTotal = Math.floor((Date.now() - startTime) / 1000); // Tempo total em segundos
    const vidasPerdidasErradas = livesLostByErrors; // Vidas perdidas por respostas erradas
    const vidasPerdidasCanvas = asteroidsLost; // Vidas perdidas por meteoros

    // Adiciona os dados ao formulário como campos ocultos
    document.getElementById('pontuacaoFinal').value = pontuacaoFinal;
    document.getElementById('tempoTotal').value = tempoTotal;
    document.getElementById('vidas_perdidas_erradas').value = vidasPerdidasErradas;
    document.getElementById('vidas_perdidas_canvas').value = vidasPerdidasCanvas;
    document.getElementById('mensagem').value = gameOverMessageSend;
};

function checkAnswer(input) {
    if (gameOver) return;

    const index = asteroids.findIndex(asteroid => asteroid.result == input);
    if (index !== -1) {
        score++;
        asteroids.splice(index, 1);
    } else {
        livesLostByErrors++;
        lives--; // Decrementa vidas gerais
        checkGameOver();
    }
};

function restartGame() {
    score = 0;
    lives = 5;
    asteroids.length = 0;
    gameOver = false;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    startAsteroidGeneration();
    gameLoop(); // Reinicia o loop de animação
};

function startAsteroidGeneration() {
    function generateAsteroid() {
        if (!gameOver) {
            createAsteroid();
            setTimeout(generateAsteroid, 4000); // gera um novo asteroide a cada x
        }
    }
    generateAsteroid();
};

function gameLoop() {
    if (!gameOver) {
        updateGame();
        requestAnimationFrame(gameLoop);
    }
};

function startGame() {
    startTime = Date.now(); // Inicializa o tempo do jogo
    startAsteroidGeneration();
    gameLoop();
};

document.getElementById('inputResposta').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        if (gameOver) {
            restartGame();
        } else {
            const input = e.target.value;
            if (input.length >= 1) {
                checkAnswer(input);
                e.target.value = ''; // Limpa o campo de texto
            }
        }
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && gameOver) {
        restartGame();
    }
});