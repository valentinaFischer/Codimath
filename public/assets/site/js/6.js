const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');
let score = 0;
let targets = [];
let question = '';
let scoreSaved = false;
let startTime;
const backgroundImage = new Image(); // cria uma nova imagem

// define posições fixas para os alvos
const fixedPositions = [
    { x: 280, y: 430 },
    { x: 480, y: 420 },
    { x: 680, y: 413 }
];

function generateRandomNumber(max) {
    return Math.floor(Math.random() * max);
}

function generateQuestion() {
    const num1 = generateRandomNumber(11);
    const num2 = generateRandomNumber(11);
    const correctAnswer = num1 * num2;
    question = `${num1} x ${num2}?`;

    if (typeof startTime === 'undefined') {
        startTime = Date.now(); // Define a hora de início do jogo
    }

    targets = [];
    const correctPositionIndex = generateRandomNumber(fixedPositions.length);
    
    for (let i = 0; i < fixedPositions.length; i++) {
        const { x, y } = fixedPositions[i];
        let answer;

        if (i === correctPositionIndex) {
            answer = correctAnswer;
        } else {
            do {
                answer = correctAnswer + generateRandomNumber(21) - 10;
            } while (answer === correctAnswer || answer < 0);
        }

        targets.push({ x, y, answer, correct: i === correctPositionIndex });
    }
};

function endGame() {
    if (!scoreSaved) {
        // Preenche os campos do formulário
        document.getElementById("pontuacaoFinal").value = score;
        document.getElementById("tempoTotal").value = Math.floor((Date.now() - startTime) / 1000); // Assume que startTime é a hora de início do jogo

        // Submete o formulário
        document.getElementById("formPontuacao").submit();
        scoreSaved = true; // Garante que o score não seja salvo mais de uma vez
    }
};

function drawBackground() {
    // desenha a imagem de fundo
    ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);
};

function drawTargets() {
    drawBackground(); // desenha o fundo antes de desenhar outros elementos

    ctx.font = '24px Arial';
    ctx.fillStyle = '#000000'; // Cor do texto
    // Ajusta as coordenadas para posicionar o texto no centro da plaquinha
    const textX = 136; // Centraliza horizontalmente
    const textY = 120; // Ajusta verticalmente para ficar na plaquinha
    ctx.fillText(question, textX, textY);

    targets.forEach(target => {
        // Preenche o círculo do alvo com a cor amarela
        ctx.beginPath();
        ctx.arc(target.x, target.y, 20, 0, 2 * Math.PI); // Ajuste o tamanho do círculo conforme necessário
        ctx.fillStyle = '#fbb263'; // Cor do preenchimento do alvo
        ctx.fill();

        // Centraliza o número no alvo
        ctx.fillStyle = '#000000'; // Cor do texto
        const answerTextWidth = ctx.measureText(target.answer).width;
        ctx.fillText(target.answer, target.x - answerTextWidth / 2, target.y + 8);
    });
};

canvas.addEventListener('mousemove', function(e) {
    const rect = canvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    let overTarget = false;

    targets.forEach(target => {
        const dist = Math.sqrt((mouseX - target.x) ** 2 + (mouseY - target.y) ** 2);
        if (dist <= 40) {
            overTarget = true;
        }
    });

    if (overTarget) {
        canvas.style.cursor = 'pointer';
    } else {
        canvas.style.cursor = 'default';
    }
});

function update() {
    drawTargets(); // Atualiza o canvas com novos alvos e perguntas
};

function handleShot(x, y) {
    let hitTarget = false;
    targets.forEach(target => {
        const dx = x - target.x;
        const dy = y - target.y;
        if (Math.sqrt(dx * dx + dy * dy) < 40) {
            hitTarget = true;
            if (target.correct) {
                score++;
                document.getElementById("score").innerText = `Pontos: ${score}`;
                generateQuestion(); // Gera nova pergunta e alvos
            } else {
                endGame(); // Chama a função de fim de jogo
                score = 0;
                document.getElementById("score").innerText = `Pontos: ${score}`;
                generateQuestion(); // Reinicia o jogo
            }
            update(); // Atualiza o canvas após uma ação
        }
    });

    if (!hitTarget) {
        update(); // Garante que o canvas seja desenhado mesmo quando não há acerto
    }
};

// Define a imagem de fundo e a função para carregar a imagem
backgroundImage.src = '/assets/site/images/fundo6.png'; // Substitua pelo caminho da sua imagem
backgroundImage.onload = () => {
    generateQuestion(); // Gera a primeira pergunta ao iniciar o jogo
    update(); // Desenha os alvos e a pergunta inicial
};

canvas.addEventListener('click', (e) => {
    const rect = canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    handleShot(x, y);
});