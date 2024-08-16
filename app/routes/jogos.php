<?php
namespace app\routes;

use app\controllers\JogosController;

$app->get('/jogo/{idAluno}/{idJogo}', JogosController::class . ":index");

$app->post('/salvarPontuacao/{aluno_id}/{professor_usuario_id}/{jogo_id}', JogosController::class . ":salvarPontuacao");