/* Jogo da Memória */

const board = document.getElementById('game-board');
const messageDiv = document.getElementById('message');
const scoreDiv = document.getElementById('scoreMemoria');
const numbers = [1, 2, 3, 4, 5, 6, 7, 8];
let cards = [...numbers, ...numbers]; // Criar pares de números
let score = 0;
    
const startTime = new Date(); 

function checkForEndGame() {
    const matchedCards = document.querySelectorAll('.matched').length;
    if (matchedCards === cards.length) {
        endGame();
    }
}

function endGame() {
    const endTime = new Date(); 
    const totalTime = Math.floor((endTime - startTime) / 1000); 
    
    document.getElementById('pontuacaoFinal').value = score;
    document.getElementById('tempoTotal').value = totalTime;
    document.getElementById('mensagem').value = 'Você ganhou! Pressione Enter para jogar de novo';
    
    document.getElementById('formPontuacao').submit();
}

        // Dicionário para converter números em texto
        const numExtenso = {
            2: 'Dois',
            4: 'Quatro',
            6: 'Seis',
            8: 'Oito',
            10: 'Dez',
            12: 'Doze',
            14: 'Quatorze',
            16: 'Dezesseis'
        };
    
        // Embaralhar as cartas
        function shuffle(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }
    
        shuffle(cards);
    
        // Criar o tabuleiro de jogo
        function createBoard() {
            cards.forEach((number) => {
                const card = document.createElement('div');
                card.classList.add('cardMemoria');
                card.dataset.number = number;
    
                const numberElement = document.createElement('div');
                numberElement.classList.add('number');
                numberElement.innerText = number;
                card.appendChild(numberElement);
    
                card.addEventListener('click', flipCard);
                board.appendChild(card);
            });
        }
    
        let firstCard, secondCard;
        let hasFlippedCard = false;
        let lockBoard = false;
    
        function flipCard() {
            if (lockBoard) return;
            if (this === firstCard) return;
    
            this.classList.add('flipped');
    
            if (!hasFlippedCard) {
                hasFlippedCard = true;
                firstCard = this;
                return;
            }
    
            secondCard = this;
            lockBoard = true;
    
            checkForMatch();
        }
    
        function checkForMatch() {
            if (firstCard.dataset.number === secondCard.dataset.number) {
                const number = parseInt(firstCard.dataset.number);
                showSumMessage(number);
                updateScore(1); // Incrementa a pontuação em 1
                disableCards();
            } else {
                unflipCards();
            }
        }
    
        function showSumMessage(number) {
            const soma = number * 2;
            const somaExtenso = numExtenso[soma];
            messageDiv.innerText = `Par encontrado! Soma: ${somaExtenso}`;
        }
    
        function updateScore(points) {
            score += points;
            scoreDiv.innerText = `Pontuação: ${score}`;
        }
    
        function disableCards() {
            firstCard.classList.add('matched');
            secondCard.classList.add('matched');
            resetBoard();
            checkForEndGame(); 
        }
    
        function unflipCards() {
            setTimeout(() => {
                firstCard.classList.remove('flipped');
                secondCard.classList.remove('flipped');
                resetBoard();
            }, 1000);
        }
    
        function resetBoard() {
            [hasFlippedCard, lockBoard] = [false, false];
            [firstCard, secondCard] = [null, null];
        }
    
        createBoard();