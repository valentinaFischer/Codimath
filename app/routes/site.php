<?php
namespace app\routes;

use app\controllers\Game;
use app\controllers\Home;
use app\controllers\Aluno;
use app\controllers\Turma;
use app\controllers\Portal;
use app\controllers\Gerencie;
use app\controllers\CriarTurma;

$app->get('/', Home::class . ":index");

$app->get('/portal', Portal::class . ":index")->add($logged);

$app->get('/gerencie', Gerencie::class . ":index")->add($logged);

$app->get('/criarTurma', CriarTurma::class . ":index")->add($logged);
$app->post('/criarTurma', CriarTurma::class . ":store");

$app->post('/adicionarAluno', CriarTurma::class . ":adicionarAluno");

$app->get('/aluno/edit/{id}', Aluno::class . ":index")->add($logged);
$app->put('/aluno/update/{id}', Aluno::class . ":update");
$app->delete('/aluno/delete/{id}', Aluno::class . ":destroy");

$app->get('/turma/edit/{id}', Turma::class . ":index")->add($logged);
$app->put('/turma/update/{id}', Turma::class . ":update");
$app->delete('/turma/delete/{id}', Turma::class . ":destroy");

$app->get('/gerencie/alunos/{id}', Gerencie::class . ":alunos");

$app->get('/gerencie/desempenho/{id}', Gerencie::class . ":desempenho");
