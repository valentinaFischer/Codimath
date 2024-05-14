<?php
namespace app\routes;

use app\controllers\Entrar;

$app->get('/cadastro', Entrar::class . ":create");
$app->post('/cadastro/store', Entrar::class . ":store");