<?php
namespace app\routes;

use app\controllers\RotaAluno;
use app\controllers\JogosEscolher;

$app->get('/turma/{idTurma}', RotaAluno::class . ":index");

$app->get('/jogos/{idAluno}', JogosEscolher::class . ":index");