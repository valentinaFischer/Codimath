const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');

// Crie uma nova imagem para o fundo
const backgroundImage = new Image();
backgroundImage.src = '/public/assets/site/images/fundo2.png'; // Substitua pelo caminho da sua imagem de fundo

const numbers = Array.from({ length: 51 }, (_, i) => ({
    word: numberToWord(i),
    value: i
}));

let score = 0;
let correctNumber;
let startTime;
let gameOver = false;
const form = document.getElementById('formPontuacao');

// Função para desenhar a imagem de fundo
function drawBackground() {
    ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);
}

function numberToWord(num) {
    const units = ["Zero", "Um", "Dois", "Três", "Quatro", "Cinco", "Seis", "Sete", "Oito", "Nove"];
    const tens = ["", "", "Vinte", "Trinta", "Quarenta", "Cinquenta"];
    const teens = ["Dez", "Onze", "Doze", "Treze", "Quatorze", "Quinze", "Dezesseis", "Dezessete", "Dezoito", "Dezenove"];
    
    if (num < 10) return units[num];
    else if (num >= 10 && num < 20) return teens[num - 10];
    else {
        let unit = num % 10;
        let ten = Math.floor(num / 10);
        return unit === 0 ? tens[ten] : `${tens[ten]} e ${units[unit]}`;
    }
}

function startGame() {
    if (!startTime) startTime = new Date();
    if (gameOver) return; // Se o jogo acabou, não faça nada

    drawBackground(); // Desenhe o fundo do canvas

    ctx.font = '30px Arial';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    const randomIndex = Math.floor(Math.random() * numbers.length);
    const chosenNumber = numbers[randomIndex];
    correctNumber = chosenNumber.value;

    ctx.fillStyle = '#007bff';
    ctx.fillText(chosenNumber.word, canvas.width / 2, canvas.height / 4);

    const shuffledNumbers = shuffleArray(numbers).slice(0, 4);
    if (!shuffledNumbers.some(number => number.value === chosenNumber.value)) {
        shuffledNumbers.pop();
        shuffledNumbers.push(chosenNumber);
    }

    shuffledNumbers.sort(() => Math.random() - 0.5);

    const buttonWidth = 100;
    const buttonHeight = 60;
    const buttonMargin = 20;

    const startX = (canvas.width - (2 * buttonWidth + buttonMargin)) / 2;
    const startY = (canvas.height - (2 * buttonHeight + buttonMargin)) / 2;

    shuffledNumbers.forEach((number, index) => {
        const x = startX + (index % 2) * (buttonWidth + buttonMargin);
        const y = startY + Math.floor(index / 2) * (buttonHeight + buttonMargin);
        drawButton(x, y, buttonWidth, buttonHeight, number.value);
    });
}

function shuffleArray(array) {
    return array.sort(() => Math.random() - 0.5);
}

function getPastelColor() {
    const hue = Math.floor(Math.random() * 360);
    const saturation = 60 + Math.random() * 20; // Range: 60-80
    const lightness = 85 + Math.random() * 10;  // Range: 85-95
    return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
}

function drawButton(x, y, width, height, value) {
    const buttonColor = getPastelColor();

    ctx.fillStyle = buttonColor;
    ctx.fillRect(x, y, width, height);
    
    ctx.fillStyle = '#000';
    ctx.fillText(value, x + width / 2, y + height / 2);

    canvas.addEventListener('click', (event) => {
        const rect = canvas.getBoundingClientRect();
        const mouseX = event.clientX - rect.left;
        const mouseY = event.clientY - rect.top;

        if (mouseX >= x && mouseX <= x + width &&
            mouseY >= y && mouseY <= y + height) {
            checkAnswer(value);
        }
    }, { once: true }); 
}

function checkAnswer(value) {
    if (value === correctNumber) {
        score++; // Incrementa o score se estiver correto

        // Limpa o canvas inteiro antes de desenhar a nova pergunta
        setTimeout(() => {
            ctx.clearRect(0, 0, canvas.width, canvas.height); // Limpa o canvas inteiro
            startGame(); // Atualiza a tela com uma nova pergunta
        }, 100); // Atraso para permitir que a resposta seja processada, ajuste conforme necessário
    } else {
        gameOver = true; // Marca o jogo como terminado
        submitGameOver(); // Submete o formulário com as informações de game over
    }
}


function submitGameOver() {
    const totalTime = Math.floor((new Date() - startTime) / 1000);
    document.getElementById('pontuacaoFinal').value = score;
    document.getElementById('tempoTotal').value = totalTime;
    document.getElementById('mensagem').value = `A resposta era ${correctNumber}`;
    form.submit();
}

// Aguarde a imagem de fundo ser carregada antes de iniciar o jogo
backgroundImage.onload = () => {
    startGame();
};