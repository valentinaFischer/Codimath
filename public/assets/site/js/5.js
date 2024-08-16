const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');
const fishes = [];
let score = 0;
let gameOver = false;
let gameOverMessage = '';
let scoreSaved = false;
let startTime;
let totalTime;

const backgroundImage = new Image();
backgroundImage.src = '/assets/site/images/fundo5.png'; // Imagem de fundo

const netImage = new Image();
netImage.src = '/assets/site/images/net.png'; // Imagem da rede

const net = {
    x: canvas.width / 2,
    y: canvas.height - 70, // Ajuste conforme a altura da imagem da rede
    width: 60,
    height: 60
};

const fishImages = [
    '/assets/site/images/fish1.png',
    '/assets/site/images/fish2.png',
    '/assets/site/images/fish3.png',
    '/assets/site/images/fish4.png'
];

function endGame() {
    if (!scoreSaved) {
        const endTime = Date.now();
        const tempoTotal = Math.floor((endTime - startTime) / 1000); // Calcula o tempo total em segundos

        document.getElementById("pontuacaoFinal").value = score;
        document.getElementById("tempoTotal").value = tempoTotal;
        document.getElementById("formPontuacao").submit();
        scoreSaved = true; // Marca que a pontuação foi salva
    }
}

function randomFish() {
    const isNumber = Math.random() < 0.2; // 20% chance de ser um número
    const numberOrLetter = isNumber ? Math.floor(Math.random() * 100) : getRandomLetter();
    const image = new Image();
    image.src = fishImages[Math.floor(Math.random() * fishImages.length)];

    let x, y, overlap;
    do {
        overlap = false;
        x = Math.random() * (canvas.width - 50);
        y = 0;
        
        for (let fish of fishes) {
            if (Math.abs(x - fish.x) < 50 && Math.abs(y - fish.y) < 50) {
                overlap = true;
                break;
            }
        }
    } while (overlap);

    return {
        x: x,
        y: y,
        width: 50,
        height: 50,
        value: numberOrLetter,
        speed: 1, // Ajuste a velocidade aqui
        image: image
    };
}

function getRandomLetter() {
    const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return letters[Math.floor(Math.random() * letters.length)];
}

function drawFish(fish) {
    ctx.drawImage(fish.image, fish.x, fish.y, fish.width, fish.height);
    ctx.fillStyle = 'black';
    ctx.font = '20px Arial';
    ctx.fillText(fish.value, fish.x + fish.width / 4, fish.y + fish.height / 1.5);
}

function drawNet() {
    ctx.drawImage(netImage, net.x, net.y, net.width, net.height);
}

function updateFish(fish) {
    fish.y += fish.speed;
    if (fish.y > canvas.height) {
        if (typeof fish.value === 'number') {
            gameOver = true;
            gameOverMessage = `Você perdeu! ${fish.value} é um número`;
        }
        // Remove o peixe da tela e garante que ele não recomece no topo
        fishes.splice(fishes.indexOf(fish), 1);
    }
}

function detectCollision(fish) {
    return fish.x < net.x + net.width &&
           fish.x + fish.width > net.x &&
           fish.y < net.y + net.height &&
           fish.y + fish.height > net.y;
}

function updateGame() {
    if (gameOver) {
        totalTime = Math.floor((Date.now() - startTime) / 1000); // Calcula o tempo total em segundos

        ctx.fillStyle = 'rgba(0, 0, 0, 0.5)'; // Tela escura
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = 'white';
        ctx.font = '30px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(gameOverMessage, canvas.width / 2, canvas.height / 2 - 30);
        ctx.font = '20px Arial';
        ctx.fillText('Pressione Enter para jogar de novo', canvas.width / 2, canvas.height / 2 + 30);

        endGame(); 

        return;
    }

    // Desenha o fundo
    ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);

    drawNet();

    // Atualiza e desenha todos os peixes
    for (let i = 0; i < fishes.length; i++) {
        updateFish(fishes[i]);
        drawFish(fishes[i]);

        if (detectCollision(fishes[i])) {
            if (typeof fishes[i].value === 'string') {
                gameOverMessage = `Você perdeu! ${fishes[i].value} é uma letra`;
                gameOver = true; // Fim de jogo
            } else {
                score += 1; // Adiciona 1 ponto ao pegar um número
            }
            fishes.splice(i, 1);
            i--;
        }
    }

    // Desenha o score em uma posição fixa no canto superior esquerdo
    ctx.fillStyle = 'black';
    ctx.font = '20px Arial';
    ctx.textAlign = 'left';
    ctx.fillText(`Score: ${score}`, 10, 20);

    requestAnimationFrame(updateGame);
}

function generateFish() {
    if (gameOver) return;

    const fish = randomFish();
    fishes.push(fish);
}



function startGame() {
    fishes.length = 0; // Limpa os peixes existentes
    score = 0;
    gameOver = false;
    gameOverMessage = '';
    startTime = Date.now(); // Marca o início do jogo
    setInterval(generateFish, 2000); // Gera um peixe a cada 2 segundos
    updateGame();
};

function restartGame() {
    startGame();
}

canvas.addEventListener('mousemove', (e) => {
    const rect = canvas.getBoundingClientRect();
    net.x = e.clientX - rect.left - net.width / 2;
});

document.addEventListener('keydown', (e) => {
    if (gameOver && e.key === 'Enter') {
        restartGame();
    }
});

// Carrega a imagem de fundo e da rede antes de iniciar o jogo
backgroundImage.onload = function() {
    netImage.onload = function() {
        startGame();
    };
};
