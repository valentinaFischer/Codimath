<?php

session_start();

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use app\classes\TwigGlobal;
use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;

require '../vendor/autoload.php';

$app = AppFactory::create();

TwigGlobal::set('is_logged_in', $_SESSION['is_logged_in'] ?? '');
TwigGlobal::set('user', $_SESSION['user_logged_data'] ?? '');

require '../app/helpers/config.php';
require '../app/middlewares/logged.php';
require '../app/routes/user.php';
require '../app/routes/site.php';
require '../app/routes/entrar.php';
require '../app/routes/aluno.php';
require '../app/routes/jogos.php';

$methodOverrideMiddleware = new MethodOverrideMiddleware();
$app->add($methodOverrideMiddleware); //para poder trabalhar com PUT e DELETE, já que o HTML não tem suporte para isso

$app->map(['GET', 'POST', 'DELETE', 'PATCH', 'PUT'], '/{routes:.+}', function ($request, $response) {
    $response->getBody()->write('Este endereço não existe');
    return $response;
});

$app->run();