/*Jogo da Cobrinha*/

/*CLASSES E FUNÇÕES */

class Obj {
    constructor(x, y, width, height) {
        this.x = x;
        this.y = y;
        this.width = width;
        this.height = height;
    }
    draw() {
       /*pode ser sobrescrita em subclasses*/
    }
};

class Snake {
    constructor(x, y, size, headImage) {
        this.size = size;
        this.body = [{ x: x, y: y }];
        this.headImage = new Image();
        this.headImage.src = headImage;
        this.dir = { x: 0, y: 0 };
    }

    move() {
        if (this.dir.x === 0 && this.dir.y === 0) {
            return false; // Não move se a direção for zero
        }
        let head = { x: this.body[0].x + this.dir.x * this.size, y: this.body[0].y + this.dir.y * this.size };

        // Verifica se a nova posição da cabeça colide com o próprio corpo ou com as bordas
        if (this.checkCollisionWithSelf(head) || this.checkCollisionWithBounds(head)) {
            return true; // Retorna true para indicar que o jogo deve acabar
        }

        this.body.pop(); // Remove o último segmento
        this.body.unshift(head); // Adiciona o novo segmento na frente
        return false; // Retorna false se o jogo continuar
    }

    grow() {
        let lastSegment = this.body[this.body.length - 1];
        this.body.push({ x: lastSegment.x, y: lastSegment.y }); // Adiciona um novo segmento ao final
    }

    draw(ctx) {
        ctx.drawImage(this.headImage, this.body[0].x, this.body[0].y, this.size, this.size);
        for (let i = 1; i < this.body.length; i++) {
            ctx.fillStyle = i % 2 === 0 ? "darkgreen" : "lightgreen";
            ctx.fillRect(this.body[i].x, this.body[i].y, this.size, this.size);
        }
    }

    checkCollision(obj) {
        return this.body[0].x < obj.x + obj.width &&
               this.body[0].x + this.size > obj.x &&
               this.body[0].y < obj.y + obj.height &&
               this.body[0].y + this.size > obj.y;
    }

    checkCollisionWithSelf(head) {
        for (let i = 1; i < this.body.length; i++) {
            if (this.body[i].x === head.x && this.body[i].y === head.y) {
                return true;
            }
        }
        return false;
    }

    checkCollisionWithBounds(head) {
        return head.x < 0 || head.y < 0 || head.x >= canvas.width || head.y >= canvas.height;
    }

    checkCollisionWithApple(apples) {
        for (let apple of apples) {
            if (this.checkCollision(apple)) {
                return apple; // Retorna a maçã com a qual a cobra colidiu
            }
        }
        return null; // Retorna null se não houver colisão com maçãs
    }
};

class Apple extends Obj {
    constructor(x, y, size, image, number) {
        super(x, y, size, size); // Define o tamanho da maçã
        this.image = new Image();
        this.image.src = image;
        this.number = number;
    }

    draw(ctx) {
        // Desenha a imagem da maçã com o tamanho definido
        ctx.drawImage(this.image, this.x, this.y, this.width, this.height);

        // Configura o estilo do texto
        const fontSize = 18; // Ajusta o tamanho da fonte proporcionalmente ao tamanho da maçã
        ctx.font = `${fontSize}px Arial`;
        ctx.fillStyle = "white";
        ctx.textAlign = "center"; // Alinha o texto ao centro
        ctx.textBaseline = "middle"; // Alinha o texto ao meio da linha base

        // Desenha o número no centro da imagem da maçã
        ctx.fillText(
            this.number, 
            this.x + this.width / 2, // Centro horizontal da maçã
            this.y + this.height / 2 // Centro vertical da maçã
        );
    }
};

class Game {
    constructor() {
        this.reset(); // Inicializa o jogo
    }

    reset() {
        this.snake = new Snake(100, 100, 20, "/assets/site/images/snake_head.png");
        this.apples = this.generateApples();
        this.score = 0;
        this.macasPares = 0;
        this.macasImpares = 0;
        this.colisoes = 0;
        this.gameOver = false;
        this.gameOverMessage = "";
        this.startTime = Date.now();
        this.scoreSaved = false;
    }

    endGame() {
        if (!this.scoreSaved) {
            const endTime = Date.now();
            const tempoTotal = Math.floor((endTime - this.startTime) / 1000);

            document.getElementById("pontuacaoFinal").value = this.score;
            document.getElementById("tempoTotal").value = tempoTotal;
            document.getElementById("macasPares").value = this.macasPares;
            document.getElementById("macasImpares").value = this.macasImpares;
            document.getElementById("colisoes").value = this.colisoes;

            document.getElementById("formPontuacao").submit();
            this.scoreSaved = true;
        }
    }

    generateApples() {
        let apples = [];
        let positions = this.getRandomPositions(2);
        let number1 = this.getRandomNumber(true);
        let number2 = this.getRandomNumber(false);

        let size = 40; // tamanho da maçã
        apples.push(new Apple(positions[0].x, positions[0].y, size, "/assets/site/images/apple.png", number1));
        apples.push(new Apple(positions[1].x, positions[1].y, size, "/assets/site/images/apple.png", number2));

        return apples;
    }

    getRandomPositions(count) {
        let positions = [];
        for (let i = 0; i < count; i++) {
            let x = Math.floor(Math.random() * 25) * 20;
            let y = Math.floor(Math.random() * 25) * 20;
            positions.push({ x: x, y: y });
        }
        return positions;
    }

    getRandomNumber(isOdd) {
        let number = Math.floor(Math.random() * 50) + 1;
        if (isOdd && number % 2 === 0) number += 1;
        if (!isOdd && number % 2 !== 0) number += 1;
        return number;
    }

    update() {
        if (this.gameOver) {
            this.endGame();
            return;
        }

        let appleCollision = this.snake.checkCollisionWithApple(this.apples);
        if (appleCollision) {
            if (appleCollision.number % 2 === 0) {
                this.score += 1;
                this.macasPares += 1;
                this.snake.grow();
                this.apples = this.generateApples();
            } else {
                this.macasImpares += 1;
                this.gameOverMessage = `O número ${appleCollision.number} é ímpar`;
                this.gameOver = true;
            }
            return;
        }

        let collisionDetected = this.snake.move();
        if (collisionDetected) {
            this.colisoes += 1;
            this.gameOver = true;
            this.gameOverMessage = this.snake.checkCollisionWithBounds(this.snake.body[0])
                ? "Cuidado para não colidir"
                : "Cuidado para não colidir";
        }
    }

    draw(ctx) {
        if (this.gameOver) {
            this.drawGameOverScreen(ctx);
        } else {
            ctx.clearRect(0, 0, canvas.width, canvas.height); // Limpa o canvas
            ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height); // Desenha o fundo
            this.snake.draw(ctx);
            for (let apple of this.apples) {
                apple.draw(ctx);
            }
        }
    }

    drawGameOverScreen(ctx) {
        // Desenha o fundo escurecido
        ctx.fillStyle = "rgba(0, 0, 0, 0.7)"; // Cor preta com opacidade
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Configura o estilo do texto
        ctx.font = "40px Arial";
        ctx.fillStyle = "white";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";

        // Desenha a mensagem de "Game Over"
        ctx.fillText("Você perdeu!", canvas.width / 2, canvas.height / 2 - 50);

        // Desenha a mensagem específica de fim de jogo
        if (this.gameOverMessage) {
            ctx.font = "30px Arial";
            ctx.fillText(this.gameOverMessage, canvas.width / 2, canvas.height / 2);
        }

        // Desenha o botão de reinício
        ctx.font = "25px Arial";
        ctx.fillStyle = "white";
        ctx.fillText("Pressione Enter para jogar novamente", canvas.width / 2, canvas.height / 2 + 50);
    }
};

const backgroundImage = new Image();
backgroundImage.src = '/assets/site/images/background.png';

/* LOOP DO JOGO */

var ctx = document.getElementById('canvas').getContext("2d");
var game = new Game();
var started = false; 

document.addEventListener("keydown", function(event) {
    if (!started) {
        started = true; 
    }

    if (game.gameOver && event.key === "Enter") {
        game.reset(); // Reinicia o jogo
        return;
    }

    switch(event.key) {
        case "ArrowUp":
            if (game.snake.dir.y === 0) game.snake.dir = { x: 0, y: -1 };
            break;
        case "ArrowDown":
            if (game.snake.dir.y === 0) game.snake.dir = { x: 0, y: 1 };
            break;
        case "ArrowLeft":
            if (game.snake.dir.x === 0) game.snake.dir = { x: -1, y: 0 };
            break;
        case "ArrowRight":
            if (game.snake.dir.x === 0) game.snake.dir = { x: 1, y: 0 };
            break;
    }
});

function draw() {
    ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);
    game.draw(ctx);
};

function update() {
    if (started) {
        game.update();
    }
};

function main() {
    ctx.clearRect(0, 0, canvas.width, canvas.height); // Limpa o canvas
    update();
    draw();
};

setInterval(main, 100);